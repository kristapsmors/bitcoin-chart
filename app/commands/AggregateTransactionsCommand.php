<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class AggregateTransactionsCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:aggregate-transactions';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Aggregate transactions.';

	public function getOptions()
	{
		return [
			['market', null, InputOption::VALUE_REQUIRED],
			['startdate', null, InputOption::VALUE_REQUIRED],
		];
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$markets = ExchangeData::$MARKETS;
		$pairs   = ExchangeData::$CURRENCY_PAIRS;

		DB::disableQueryLog();

		$markets[] = 'all';

		$market = $this->option('market');
		if ($market)
		{
			$markets = [$market];
		}

		foreach ($markets as $market)
		{
			foreach ($pairs as $pair)
			{
				$this->aggregate($market, $pair[0], $pair[1]);
			}
		}
	}

	protected function aggregate($market, $from_currency, $to_currency)
	{
		$this->info("Aggregating {$market}: {$from_currency} -> {$to_currency}");

		$lastDate = $this->option('startdate');

		if (!$lastDate)
		{
			/** @var $last ExchangeData */
			$last     = ExchangeData::whereMarketAndCurrencies($market, $from_currency, $to_currency)
				->orderBy('updated_at', 'desc')
				->take(1)
				->get(['updated_at'])
				->first();
			$lastDate = $last ? $last->updated_at->copy()->subMinute()->format('Y-m-d H:i:s') : '0000-00-00 00:00:00';
		}

		while (true)
		{
			/** @var $transactionDates Transaction[]|Illuminate\Database\Eloquent\Collection */
			$transactionDates = Transaction::whereMarketAndCurrencies($market, $from_currency, $to_currency, true)
				->where('updated_at', '>', $lastDate)
				->orderBy('updated_at')
				->take(2000)
				->get(['made_at', 'updated_at']);

			if (!$transactionDates->count())
			{
				return;
			}

			$uniqueDates = array_unique($transactionDates->map(function (Transaction $row)
			{
				return $row->made_at->format('Y-m-d H:i:00');
			})->toArray());
			$toUpdate    = [];

			$twoDaysAgo = strtotime('-2 days');
			$weekAgo    = strtotime('-1 week');
			$endDate    = date('Y-m-d H:i:s', 0);

			foreach ($uniqueDates as $date)
			{
				$unix = strtotime($date);
				if ($unix <= strtotime($endDate))
				{
					continue;
				}
				if ($unix < $weekAgo)
				{
					$date    = date('Y-m-d 00:00:00', $unix);
					$endDate = date('Y-m-d 23:59:59', $unix);
				}
				else if ($unix < $twoDaysAgo)
				{
					$date    = date('Y-m-d H:00:00', $unix);
					$endDate = date('Y-m-d H:59:59', $unix);
				}
				else
				{
					$endDate = $date;
				}
				$toUpdate[] = [$date, $endDate];
			}

			foreach ($toUpdate as $dates)
			{
				$this->update($market, $from_currency, $to_currency, $dates[0], $dates[1]);
			}

			$lastDate = $transactionDates->last()->updated_at->format('Y-m-d H:i:s');
		}
	}

	protected function update($market, $from_currency, $to_currency, $start_date, $end_date = null, $summary_date = null)
	{
		if (!$end_date)
		{
			$end_date = $start_date;
		}

		if (!$summary_date)
		{
			$summary_date = $start_date;
		}

		$this->comment("Updating `{$market}` ({$from_currency} -> {$to_currency}) @ {$start_date} - {$end_date}");

		/** @var $current ExchangeData */
		$current = ExchangeData::whereMarketAndCurrencies($market, $from_currency, $to_currency)
			->where('datetime', $summary_date)
			->take(1)
			->get()->first();

		if (!$current)
		{
			$current = new ExchangeData();

			$current->market        = $market;
			$current->from_currency = $from_currency;
			$current->to_currency   = $to_currency;
			$current->datetime      = $summary_date;

			$current->save();
			$current = ExchangeData::find($current->id);
		}

		// We set "updated_at" here so we don't lose transactions between here and save() moment
		$current->updated_at = new Carbon\Carbon($summary_date);

		/** @var $transactions Transaction[] */
		$transactions = Transaction::whereMarketAndCurrencies($market, $from_currency, $to_currency, true)
			->where('made_at', '>=', $start_date)
			->where('made_at', '<=', $end_date)
			->get();

		$aggregate = [
			'open'       => null,
			'close'      => 0,
			'low'        => PHP_INT_MAX,
			'high'       => 0,
			'volume'     => 0,
			'volume_cur' => 0,
			'avg'        => 0,
		];

		foreach ($transactions as $transaction)
		{
			if ($aggregate['open'] === null)
			{
				$aggregate['open'] = $transaction->open;
			}
			if ($transaction->high > $aggregate['high'])
			{
				$aggregate['high'] = $transaction->high;
			}
			if ($transaction->low < $aggregate['low'])
			{
				$aggregate['low'] = $transaction->low;
			}

			$aggregate['close'] = $transaction['close'];
			$aggregate['volume'] += $transaction->amount();
			$aggregate['volume_cur'] += $transaction->in_currency();
		}

		if ($aggregate['volume'])
		{
			$aggregate['avg'] = $aggregate['volume_cur'] / $aggregate['volume'];
		}

		foreach ($aggregate as $field => $value)
		{
			$current->{$field} = $value;
		}

		$current->save();
	}

	protected function isLocked()
	{
		$lockFile = $this->getLockFile();

		return file_exists($lockFile) && (time() - filemtime($lockFile)) < $this->getLockTime();
	}

	protected function lock()
	{
		touch($this->getLockFile());
	}

	protected function unlock()
	{
		$lockFile = $this->getLockFile();
		if (file_exists($lockFile))
		{
			unlink($lockFile);
		}
	}

	protected function getLockFile()
	{
		return '/tmp/bitcoin-charts-' . get_class($this) . '.lock';
	}

	protected function getLockTime()
	{
		return 600; // 10 minutes
	}
}

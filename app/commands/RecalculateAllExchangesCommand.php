<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class RecalculateAllExchangesCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:recalculate-all';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Recalculates totals for all exchanges.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$start = new Carbon\Carbon($this->option('start'));
		foreach (ExchangeData::$CURRENCY_PAIRS as $pair) {
			$currentStart = $start;
			list($fromCurrency, $toCurrency) = $pair;
			do
			{
				$this->info("Processing {$fromCurrency} to {$toCurrency} starting from {$currentStart->toDateTimeString()}");

				/** @var $dates ExchangeData[]|Illuminate\Database\Eloquent\Collection */
				$dates      = ExchangeData::whereCurrencies($fromCurrency, $toCurrency)
					->where('market', '<>', 'all')
					->where('datetime', '>=', $currentStart->toDateTimeString())
					->orderBy('datetime')
					->take(1000)
					->distinct()
					->get(['datetime']);
				$aggregated = [];
				/** @var $end Carbon\Carbon */
				$end = $currentStart;
				foreach ($dates as $date)
				{
					/** @var $rows ExchangeData[]|Illuminate\Database\Eloquent\Collection */
					$rows    = ExchangeData::whereCurrencies($fromCurrency, $toCurrency)
						->where('market', '<>', 'all')
						->where('datetime', $date->datetime)
						->get();
					$summary = [
						'volume'        => 0.0,
						'volume_cur'    => 0.0,
						'open'          => $rows->first()->open,
						'close'         => 0,
						'high'          => 0,
						'low'           => PHP_INT_MAX,
						'avg'           => 0,
						'market'        => 'all',
						'from_currency' => $fromCurrency,
						'to_currency'   => $toCurrency,
						'datetime'      => $date->datetime,
						'created_at'    => $date->datetime,
						'updated_at'    => $date->datetime,
					];
					foreach ($rows as $row)
					{
						$summary['close'] = $row->close;
						if ($summary['low'] > $row->low)
						{
							$summary['low'] = $row->low;
						}
						if ($summary['high'] < $row->high)
						{
							$summary['high'] = $row->high;
						}
						$summary['volume'] += $row->volume;
						$summary['volume_cur'] += $row->volume_cur;
					}
					if ($summary['volume'])
					{
						$summary['avg'] = $summary['volume_cur'] / $summary['volume'];
					}
					$aggregated[] = $summary;

					if ($date->datetime->gt($end))
					{
						$end = $date->datetime;
					}
				}
				$this->comment("Deleting {$fromCurrency} -> {$toCurrency} @ {$currentStart->toDateTimeString()} - {$end->toDateTimeString()}");
				ExchangeData::whereMarketAndCurrencies('all', $fromCurrency, $toCurrency)
					->whereBetween('datetime', [$currentStart->toDateTimeString(), $end->toDateTimeString()])
					->delete();
				foreach ($aggregated as $row)
				{
					$all = new ExchangeData();
					foreach ($row as $field => $value)
					{
						$all->{$field} = $value;
					}
					$all->save();
				}
				$this->comment("Processed {$currentStart->toDateString()} - {$end->toDateString()}");
				if ($currentStart->toDateString() === $end->toDateString())
				{
					break;
				}
				$currentStart = $end->copy();
			} while (true);
		}
	}

	public function getOptions()
	{
		return [
			['start', null, InputOption::VALUE_REQUIRED, 'Starting date', date('Y-m-d H:i:s', strtotime('-1 week'))],
		];
	}
}

<?php

use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;

class UpdateExchangesSummaryCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:update-summary';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update exchanges summary data';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		DB::connection()->disableQueryLog();

		$pairs   = ExchangeData::$CURRENCY_PAIRS;
		$markets = ExchangeData::$MARKETS;

		foreach ($pairs as $pair)
		{
			$this->summarize('all', $pair[0], $pair[1]);
		}

		foreach ($markets as $market)
		{
			foreach ($pairs as $pair)
			{
				$this->summarize($market, $pair[0], $pair[1]);
			}
		}
	}

	protected function summarize($market, $from_currency, $to_currency)
	{
		if ($market !== 'all')
		{
			$exists = (bool) $query = ExchangeData::whereMarketAndCurrencies($market, $from_currency, $to_currency)
				->count();
			if (!$exists)
			{
				return;
			}
		}
		$periods = [
			'24h' => new \DateTime('-24 hours'),
			'7d'  => new \DateTime('-7 days'),
			'1m'  => new \DateTime('-1 month'),
			'3m'  => new \DateTime('-3 month'),
			'ytd' => new \DateTime('@' . mktime(0, 0, 0, 1, 1, date('Y'))),
			'1y'  => new \DateTime('-1 year'),
			'5y'  => new \DateTime('-5 years'),
			'all' => new \DateTime('0000-00-00 00:00:00'),
		];

		foreach ($periods as $type => $date)
		{
			$this->info("Summarising {$market} ({$from_currency} -> {$to_currency}) @ {$type}");

			/** @var $query Builder */
			$query = ExchangeData::whereMarketAndCurrencies($market, $from_currency, $to_currency, true)
				->where('datetime', '>', $date)
				->orderBy('datetime');

			$aggregated = $this->aggregate($query);

			$model = ExchangeSummary::where('market', $market)
				->where('type', $type)
				->where('from_currency', $from_currency)
				->where('to_currency', $to_currency)
				->first();
			if (!$model)
			{
				$model                = new ExchangeSummary();
				$model->market        = $market;
				$model->type          = $type;
				$model->from_currency = $from_currency;
				$model->to_currency   = $to_currency;
			}

			foreach (['open', 'close', 'high', 'low', 'avg', 'volume', 'volume_cur'] as $field)
			{
				$model->{$field} = $aggregated[$field];
			}

			$model->save();
			$model = $aggregated = null;
			unset($model, $aggregated);
		}
	}

	/**
	 * @param Builder $query
	 *
	 * @return array
	 */
	protected function aggregate($query)
	{
		$aggregated = [
			'open'       => null,
			'close'      => 0,
			'high'       => 0,
			'low'        => PHP_INT_MAX,
			'avg'        => 0,
			'volume'     => 0.0,
			'volume_cur' => 0.0,
		];

		$limit  = 1000;
		$offset = 0;
		do
		{
			$query->limit($limit);
			$query->offset($offset);
			$offset += $limit;

			/** @var $data ExchangeData[] */
			$data = $query->get();
			foreach ($data as $row)
			{
				if ($aggregated['open'] === null)
				{
					$aggregated['open'] = $row->open;
				}

				$aggregated['close'] = $row->close;

				if ($row->high > $aggregated['high'])
				{
					$aggregated['high'] = $row->high;
				}

				if ($row->low < $aggregated['low'])
				{
					$aggregated['low'] = $row->low;
				}

				$aggregated['volume'] += $row->volume;
				$aggregated['volume_cur'] += $row->volume_cur;
			}
			$data = null;
		} while (count($data) > 0);

		if ($aggregated['open'] === null)
		{
			$aggregated['open'] = 0;
		}

		if ($aggregated['volume'])
		{
			$aggregated['avg'] = $aggregated['volume_cur'] / $aggregated['volume'];
		}
		else
		{
			$aggregated['high'] = 0.0;
			$aggregated['low']  = 0.0;
		}

		return $aggregated;
	}
}
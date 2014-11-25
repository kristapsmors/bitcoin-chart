<?php

use Illuminate\Console\Command;

class CompressExchangesDataCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:compress';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Compress exchanges data.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$markets = ExchangeData::$MARKETS;
		$pairs   = ExchangeData::$CURRENCY_PAIRS;

		$markets[] = 'all';
		foreach ($markets as $market)
		{
			foreach ($pairs as $pair)
			{
				$this->compress($market, $pair[0], $pair[1]);
			}
		}
	}

	protected function compress($market, $from_currency, $to_currency)
	{
		$ranges = [
			[new \DateTime('-24 hours'), new \DateTime('-7 days'), 'Y-m-d H:00:00'],
			[new \DateTime('-7 days'), new \DateTime('-30 days'), 'Y-m-d 00:00:00'],
		];

		foreach ($ranges as $range)
		{
			list ($to, $from, $format) = $range;
			/** @var $from \DateTime */
			/** @var $to \DateTime */
			$fromDate = $from->format('Y-m-d 00:00:00');
			$toDate   = $to->format('Y-m-d 00:00:00');

			$this->info("Compressing {$market} ({$from_currency} -> {$to_currency}): {$fromDate} - {$toDate}");

			/** @var $rows ExchangeData[]|Illuminate\Database\Eloquent\Collection */
			$rows = ExchangeData::whereMarketAndCurrencies($market, $from_currency, $to_currency)
				->where('datetime', '>=', $fromDate)
				->where('datetime', '<', $toDate)
				->orderBy('datetime')
				->get();

			$count = $rows->count();
			if ($count <= 1)
			{
				continue;
			}

			DB::beginTransaction();

			try
			{
				$aggregated = [];

				foreach ($rows as $row)
				{
					$groupKey = $row->datetime->format($format);
					if (!isset($aggregated[$groupKey]))
					{
						$aggregated[$groupKey] = $row->toArray();
						unset($aggregated[$groupKey]['id']);
						$aggregated[$groupKey]['created_at'] = $groupKey;
						$aggregated[$groupKey]['updated_at'] = $groupKey;
						$aggregated[$groupKey]['datetime']   = $groupKey;
						$aggregated[$groupKey]['volume']     = 0;
						$aggregated[$groupKey]['volume_cur'] = 0;
					}

					$aggregated[$groupKey]['volume'] += $row->volume;
					$aggregated[$groupKey]['volume_cur'] += $row->volume_cur;
					$aggregated[$groupKey]['close'] = $row->close;

					if ($row->high > $aggregated[$groupKey]['high'])
					{
						$aggregated[$groupKey]['high'] = $row->high;
					}
					if ($row->low < $aggregated[$groupKey]['low'])
					{
						$aggregated[$groupKey]['low'] = $row->low;
					}
				}

				ExchangeData::whereMarketAndCurrencies($market, $from_currency, $to_currency)
					->where('datetime', '>=', $fromDate)
					->where('datetime', '<', $toDate)
					->delete();

				foreach ($aggregated as $row)
				{
					if ($row['volume'])
					{
						$row['avg'] = $row['volume_cur'] / $row['volume'];
					}

					$exchangeData = new ExchangeData();
					foreach ($row as $field => $value)
					{
						$exchangeData->{$field} = $value;
					}
					$exchangeData->save();
				}

				DB::commit();

				$aggregatedCount = count($aggregated);
				$this->comment("Compressed {$count} rows into {$aggregatedCount} rows");

			} catch (\Exception $e)
			{
				DB::rollback();
			}
		}
	}
}

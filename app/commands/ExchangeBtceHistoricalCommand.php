<?php

class ExchangeBtceHistoricalCommand extends ExchangeTickerCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:historical:btce';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get historical BTC-e exchange data';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$currencies = [ExchangeData::CURRENCY_USD, ExchangeData::CURRENCY_EUR];
		foreach ($currencies as $currency)
		{
			$url      = "http://www.quandl.com/api/v1/datasets/BITCOIN/BTCE{$currency}.json?rows=10000";
			$response = file_get_contents($url);
			$result   = json_decode($response, true); // Split the JSON into arrays.

			foreach ($result['data'] as $row)
			{
				$date = $row[0];
				if ($row[2] == 1.7e+308)
				{
					continue;
				}
				$amounts = [
					'open'  => $row[1],
					'high'  => $row[2],
					'low'   => $row[3],
					'close' => $row[4],
				];
				$volumes = [
					'bitcoins' => $row[5],
					'currency' => $row[6],
				];

				$this->saveData(ExchangeData::MARKET_BTCE, ExchangeData::CURRENCY_BTC, $currency, $amounts, $volumes,
					$date);
			}

			$this->info("Historical: btce @ {$currency} | Saved historical exchange data");
		}
	}
}

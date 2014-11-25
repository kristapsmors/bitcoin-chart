<?php

class ExchangeCampbxHistoricalCommand extends ExchangeTickerCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:historical:campbx';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get historical CampBX exchange data';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$response = file_get_contents('http://www.quandl.com/api/v1/datasets/BITCOIN/CBXUSD.json?rows=10000');
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

			$this->saveData(ExchangeData::MARKET_CAMPBX, ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_USD, $amounts,
				$volumes, $date);
		}

		$this->info('Historical: campbx @ USD | Saved historical exchange data');
	}
}

<?php

class ExchangeBitstampTickerCommand extends ExchangeTickerCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:ticker:bitstamp';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get Bitstamp Exchange Data';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$response = file_get_contents('https://www.bitstamp.net/api/ticker/');
		$result   = json_decode($response, true); // Split the JSON into arrays.

		$amounts = [
			'low'  => $result['low'],
			'high' => $result['high'],
			'last' => $result['last'],
		];

		$volumes = [
			'bitcoins' => $result['volume'],
		];

		$this->saveData(ExchangeData::MARKET_BITSTAMP, ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_USD, $amounts,
			$volumes);

		$this->info('Ticker: bitstamp @ USD | Saved Exchange Data for ' . date('d.m.Y H:i:s'));
	}
}

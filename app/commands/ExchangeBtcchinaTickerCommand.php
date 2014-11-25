<?php

class ExchangeBtcchinaTickerCommand extends ExchangeTickerCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:ticker:btcchina';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get BTCChina Exchange Data';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$currencies = [ExchangeData::CURRENCY_CNY];
		foreach ($currencies as $currency)
		{
			$url    = "https://data.btcchina.com/data/ticker";
			$ticker = file_get_contents($url);

			$response = json_decode($ticker, true); // Split the JSON into arrays.
			$result   = $response['ticker'];

			$amounts = [
				'low'  => $result['low'],
				'high' => $result['high'],
				'last' => $result['last'],
			];

			$volumes = [
				'bitcoins' => $result['vol'],
			];

			$this->saveData(ExchangeData::MARKET_BTCCHINA, ExchangeData::CURRENCY_BTC, $currency, $amounts, $volumes);

			$this->info("Ticker: btcchina @ {$currency} | Saved Exchange Data for " . date('d.m.Y H:i:s'));
		}
	}
}

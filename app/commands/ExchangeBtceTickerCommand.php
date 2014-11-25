<?php

class ExchangeBtceTickerCommand extends ExchangeTickerCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:ticker:btce';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get BTCe Exchange Data';

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
			$lower  = strtolower($currency);
			$url    = "https://btc-e.com/api/2/btc_{$lower}/ticker";
			$ticker = file_get_contents($url);

			$response = json_decode($ticker, true); // Split the JSON into arrays.
			$result   = $response['ticker'];

			$amounts = [
				'low'  => $result['low'],
				'high' => $result['high'],
				'avg'  => $result['avg'],
				'last' => $result['last'],
			];

			$volumes = [
				'bitcoins' => $result['vol'],
				'currency' => $result['vol_cur'],
			];

			$this->saveData(ExchangeData::MARKET_BTCE, ExchangeData::CURRENCY_BTC, $currency, $amounts, $volumes);

			$this->info("Ticker: btc-e @ {$currency} | Saved Exchange Data for " . date('d.m.Y H:i:s'));
		}
	}
}

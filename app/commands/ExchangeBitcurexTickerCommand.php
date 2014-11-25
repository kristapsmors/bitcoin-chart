<?php

class ExchangeBitcurexTickerCommand extends ExchangeTickerCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:ticker:bitcurex';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get Bitcurex Exchange Data';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$currencies = [ExchangeData::CURRENCY_PLN, ExchangeData::CURRENCY_EUR];
		foreach ($currencies as $currency)
		{
			$lower  = strtolower($currency);
			$url    = "https://{$lower}.bitcurex.com/data/ticker.json";
			$ticker = file_get_contents($url);

			$result = json_decode($ticker, true); // Split the JSON into arrays.

			$amounts = [
				'low'  => $result['low'],
				'high' => $result['high'],
				'avg'  => $result['avg'],
				'last' => $result['last'],
			];

			$volumes = [
				'bitcoins' => $result['vol'],
			];

			$this->saveData(ExchangeData::MARKET_BITCUREX, ExchangeData::CURRENCY_BTC, $currency, $amounts, $volumes);

			$this->info("Ticker: bitcurex @ {$currency} | Saved Exchange Data for " . date('d.m.Y H:i:s'));
		}
	}
}

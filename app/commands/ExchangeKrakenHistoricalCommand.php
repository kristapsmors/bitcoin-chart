<?php

class ExchangeKrakenHistoricalCommand extends ExchangeTickerCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:historical:kraken';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get historical Kraken exchange data';

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
			$url      = "http://api.bitcoincharts.com/v1/trades.csv?symbol=kraken{$currency}";
			$response = file_get_contents($url);
			$rows     = explode("\n", $response);

			foreach ($rows as $row)
			{
				list ($date, $price, $amount) = explode(',', $row);
				$amounts = [
					'avg'   => (float) $price,
					'high'  => (float) $price,
					'low'   => (float) $price,
					'open'  => (float) $price,
					'close' => (float) $price,
				];
				$volumes = [
					'bitcoins' => (float) $amount,
				];

				$this->saveData(ExchangeData::MARKET_KRAKEN, ExchangeData::CURRENCY_BTC, $currency, $amounts, $volumes,
					$date);
			}

			$this->info("Historical: kraken @ {$currency} | Saved historical exchange data");
		}
	}
}

<?php

class ExchangeKrakenTickerCommand extends ExchangeTickerCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:ticker:kraken';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get Kraken Exchange Data';

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
			$pair = "XXBTZ{$currency}";
			// build the POST data string
			$ticker   = file_get_contents('https://api.kraken.com/0/public/Ticker?pair=' . $pair);
			$response = json_decode($ticker, true); // Split the JSON into arrays.

			if (!empty($response['errors']))
			{
				$this->error('Errors in response: ' . var_export($response['errors'], true));

				return;
			}

			$result = $response['result'][$pair];

			$amounts = [
				'open'  => (float) $result['o'],
				'low'   => (float) $result['l'][0],
				'high'  => (float) $result['h'][0],
				'close' => (float) $result['c'][0],
				'last'  => (float) $result['c'][0],
				'avg'   => (float) $result['p'][0],
			];

			$volumes = [
				'bitcoins' => $result['v'][0],
			];

			$this->saveData(ExchangeData::MARKET_KRAKEN, ExchangeData::CURRENCY_BTC, $currency, $amounts, $volumes);

			$this->info("Ticker: kraken @ {$currency} | Saved Exchange Data for " . date('d.m.Y H:i:s'));
		}
	}
}

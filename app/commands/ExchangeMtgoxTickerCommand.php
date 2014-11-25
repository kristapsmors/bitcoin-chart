<?php

class ExchangeMtgoxTickerCommand extends ExchangeTickerCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:ticker:mtgox';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get MtGox Exchange Data';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$currencies = [
			ExchangeData::CURRENCY_USD, ExchangeData::CURRENCY_CNY, ExchangeData::CURRENCY_EUR,
			ExchangeData::CURRENCY_JPY, ExchangeData::CURRENCY_PLN, ExchangeData::CURRENCY_GBP,
		];
		foreach ($currencies as $currency)
		{
			$url      = "http://data.mtgox.com/api/2/BTC{$currency}/money/ticker";
			$ticker   = file_get_contents($url);
			$response = json_decode($ticker, true); // Split the JSON into arrays.
			if ($response['result'] !== 'success')
			{
				$this->error('Unsuccessful response', $ticker);

				return;
			}
			$result = $response['data'];

			$amounts = [
				'high' => (float) $result['high']['value'],
				'low'  => (float) $result['low']['value'],
				'avg'  => (float) $result['avg']['value'],
				'last' => (float) $result['last_local']['value']
			];

			$volumes = [
				'bitcoins' => (float) $result['vol']['value'],
			];

			$fromCurrency = $result['item'];
			$toCurrency   = $result['last_local']['currency'];
			$this->saveData(ExchangeData::MARKET_MTGOX, $fromCurrency, $toCurrency, $amounts, $volumes);

			$this->info("Ticker: mtgox @ {$currency} | Saved Exchange Data for " . date('d.m.Y H:i:s'));
		}
	}
}

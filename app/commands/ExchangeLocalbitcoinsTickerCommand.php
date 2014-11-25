<?php

class ExchangeLocalbitcoinsTickerCommand extends ExchangeTickerCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:ticker:localbitcoins';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get LocalBitcoins Exchange Data';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$ticker     = file_get_contents('https://localbitcoins.com/bitcoinaverage/ticker-all-currencies/');
		$response   = json_decode($ticker, true); // Split the JSON into arrays.
		$currencies = [
			ExchangeData::CURRENCY_USD, ExchangeData::CURRENCY_EUR, ExchangeData::CURRENCY_JPY,
			ExchangeData::CURRENCY_PLN, ExchangeData::CURRENCY_GBP, ExchangeData::CURRENCY_CAD,
		];
		foreach ($currencies as $currency)
		{
			$result = $response[$currency];
			$last   = (float) $result['rates']['last'];
			$avg    = $result['avg_24h'];
			if (!$avg)
			{
				continue;
			}
			if ($last > $avg)
			{
				$high = $last;
				$low  = $avg;
			}
			else
			{
				$low  = $last;
				$high = $avg;
			}
			$amounts = [
				'high' => $high,
				'low'  => $low,
				'avg'  => $avg,
				'last' => $last,
			];
			$volumes = [
				'bitcoins' => $result['volume_btc'],
			];

			$this->saveData(ExchangeData::MARKET_LOCALBITCOINS, ExchangeData::CURRENCY_BTC, $currency, $amounts,
				$volumes);

			$this->info("Ticker: localbitcoins @ {$currency} | Saved Exchange Data for " . date('d.m.Y H:i:s'));
		}
	}
}

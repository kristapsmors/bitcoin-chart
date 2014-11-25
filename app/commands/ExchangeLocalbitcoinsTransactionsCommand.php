<?php

class ExchangeLocalbitcoinsTransactionsCommand extends ExchangeTransactionsCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:transactions:localbitcoins';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Gather LocalBitcoins transactions data';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		if ($this->isLocked())
		{
			$this->error('Script is already running');

			return;
		}

		$this->lock();

		DB::disableQueryLog();

		$market = ExchangeData::MARKET_LOCALBITCOINS;
		$pairs  = [
			[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_USD],
			[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_EUR],
			[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_JPY],
			[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_PLN],
			[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_GBP],
			[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_CAD],
		];

		foreach ($pairs as $pair)
		{
			list($from_currency, $to_currency) = $pair;
			$this->info("Collecting {$market} ({$from_currency} -> {$to_currency})");
			$lastTid = $this->getLastId($market, $from_currency, $to_currency);
			do
			{
				$url = "https://localbitcoins.com/bitcoincharts/{$to_currency}/trades.json?since={$lastTid}";

				$response = file_get_contents($url);
				$result   = json_decode($response);
				$count    = count($result);

				foreach ($result as $row)
				{
					$date   = date('Y-m-d H:i:00', $row->date);
					$tid    = $row->tid;
					$amount = (float) $row->amount;
					$price  = (float) $row->price;

					$lastTid = $tid;

					$this->aggregate($market, $from_currency, $to_currency, $date, $amount, $price, $tid);
					$row = null;
				}

				$this->flush();

				$this->line("Collected {$count} entries");
				$result = $response = null;
				unset($result, $response);
			} while ($count);
		}

		$this->unlock();
	}
}

<?php

class ExchangeBtcchinaTransactionsCommand extends ExchangeTransactionsCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:transactions:btcchina';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Gather BTCChina transactions data';

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

		$market = ExchangeData::MARKET_BTCCHINA;
		$pairs  = [
			[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_CNY],
		];

		foreach ($pairs as $pair)
		{
			list($from_currency, $to_currency) = $pair;
			$url = "https://data.btcchina.com/data/trades";

			$response = file_get_contents($url);
			$result   = json_decode($response);

			foreach ($result as $row)
			{
				$date   = date('Y-m-d H:i:00', (int) $row->date);
				$tid    = $row->tid;
				$amount = (float) $row->amount;
				$price  = (float) $row->price;

				$this->aggregate($market, $from_currency, $to_currency, $date, $amount, $price, $tid);
			}

			$this->flush();
		}

		$this->unlock();
	}
}

<?php

class ExchangeBtceTransactionsCommand extends ExchangeTransactionsCommandBase
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:transactions:btce';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Gather BTC-e transactions data';

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

		$market = ExchangeData::MARKET_BTCE;
		$pairs  = [
			['BTC', 'USD'],
			['BTC', 'EUR'],
		];

		foreach ($pairs as $pair)
		{
			list($from_currency, $to_currency) = $pair;
			$urlPart = strtolower("{$from_currency}_{$to_currency}");
			$url     = "https://btc-e.com/api/2/{$urlPart}/trades?limit=1000";

			$response = file_get_contents($url);
			$result   = json_decode($response);

			for ($i = count($result) - 1; $i >= 0; $i--)
			{
				$row = $result[$i];

				$date   = date('Y-m-d H:i:00', $row->date);
				$tid    = $row->tid;
				$amount = (float) $row->amount;
				$price  = (float) $row->price;

				$this->aggregate($market, $from_currency, $to_currency, $date, $amount, $price, $tid, $row->trade_type == 'ask');
			}

			$this->flush();
		}

		$this->unlock();
	}
}

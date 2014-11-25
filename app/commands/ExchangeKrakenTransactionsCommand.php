<?php

class ExchangeKrakenTransactionsCommand extends ExchangeTransactionsCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:transactions:kraken';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Gather Kraken transactions data';

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

		$market = ExchangeData::MARKET_KRAKEN;
		$pairs  = [
			['BTC', 'USD', 'XBT'],
			['BTC', 'EUR', 'XBT'],
		];

		foreach ($pairs as $pair)
		{
			list($from_currency, $to_currency, $symbol) = $pair;
			$urlPart = "X{$symbol}Z{$to_currency}";
			$url     = "https://api.kraken.com/0/public/Trades?pair={$urlPart}";

			$response = file_get_contents($url);
			$result   = json_decode($response);
			$result   = $result->result->{$urlPart};

			foreach ($result as $row)
			{
				$date   = date('Y-m-d H:i:00', $row[2]);
				$tid    = $row[2] * 100000;
				$price  = (float) $row[0];
				$amount = (float) $row[1];

				$this->aggregate($market, $from_currency, $to_currency, $date, $amount, $price, $tid, $row[3] == 's');
			}

			$this->flush();
		}

		$this->unlock();
	}
}

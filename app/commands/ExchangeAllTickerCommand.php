<?php

class ExchangeAllTickerCommand extends ExchangeTickerCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:ticker:all';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Get All Exchanges Data';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$exchanges = ['bitstamp', 'btce', 'mtgox', 'localbitcoins', 'kraken', 'campbx', 'bitcurex', 'btcchina'];
		foreach ($exchanges as $exchange)
		{
			try
			{
				$this->call("bc:ticker:{$exchange}");
			} catch (\Exception $e)
			{
				$this->error("Error during {$exchange}: {$e->getMessage()}");
			}
		}
	}
}

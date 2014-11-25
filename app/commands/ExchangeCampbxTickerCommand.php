<?php

class ExchangeCampbxTickerCommand extends ExchangeTickerCommandBase {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bc:ticker:campbx';

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
		$response = file_get_contents('http://campbx.com/api/xticker.php');
		$result   = json_decode($response, true);

		$amount = (float) $result['Last Trade'];

		$amounts = [
			'low'  => $amount,
			'high' => $amount,
			'avg'  => $amount,
			'last' => $amount,
		];

		$volumes = [
			'bitcoins' => 0,
		];

		$this->saveData(ExchangeData::MARKET_CAMPBX, ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_USD, $amounts,
			$volumes);

		$this->info('Ticker: campbx @ USD | Saved Exchange Data for ' . date('d.m.Y H:i:s'));
	}
}

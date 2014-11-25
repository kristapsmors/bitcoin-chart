<?php

use Illuminate\Console\Command;

class ExchangeTickerCommandBase extends Command
{
	/**
	 * @param string                              $market
	 * @param string                              $from_currency
	 * @param string                              $to_currency
	 * @param array                               $amounts
	 * @param array                               $volumes
	 * @param int|string|\DateTime|\Carbon\Carbon $datetime
	 */
	protected function saveData($market, $from_currency, $to_currency, array $amounts, array $volumes = [], $datetime = null)
	{
		$amounts = array_merge(
			['open' => null, 'close' => null, 'high' => null, 'low' => null, 'avg' => null, 'last' => null],
			$amounts);
		$volumes = array_merge(['bitcoins' => null, 'currency' => null], $volumes);

		$volume     = $volumes['bitcoins'];
		$volume_cur = $volumes['currency'];

		$average = $amounts['avg'];
		$low     = $amounts['low'];
		$high    = $amounts['high'];
		$open    = $amounts['open'];
		$close   = $amounts['close'];
		$last    = $amounts['last'];

		if ($average === null)
		{
			$average = ($low + $high) / 2;
		}

		if ($volume === null && $volume_cur !== null)
		{
			$volume = $volume_cur / $average;
		}
		else if ($volume_cur === null && $volume !== null)
		{
			$volume_cur = $volume * $average;
		}

		$datetime = $datetime !== null ? $datetime : new \DateTime();

		$exchange_data                = new ExchangeData();
		$exchange_data->market        = $market;
		$exchange_data->from_currency = $from_currency;
		$exchange_data->to_currency   = $to_currency;
		$exchange_data->low           = $low;
		$exchange_data->high          = $high;
		$exchange_data->avg           = $average;
		$exchange_data->open          = $open;
		$exchange_data->close         = $close;
		$exchange_data->last          = $last;
		$exchange_data->volume        = $volume;
		$exchange_data->volume_cur    = $volume_cur;
		$exchange_data->datetime      = $datetime;
		$exchange_data->save();
	}
}
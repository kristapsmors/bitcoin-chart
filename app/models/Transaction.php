<?php

/**
 * Class ExchangeData
 *
 * @property string                                   market
 * @property string                                   from_currency
 * @property string                                   to_currency
 * @property \Carbon\Carbon|\DateTime|int|null|string made_at
 * @property float                                    in_currency_buy
 * @property float                                    in_currency_sell
 * @property float                                    amount_buy
 * @property float                                    amount_sell
 * @property float                                    count_buy
 * @property float                                    count_sell
 * @property string                                   last_transaction_id
 * @property \Carbon\Carbon|\DateTime|int|null|string updated_at
 * @property \Carbon\Carbon|\DateTime|int|null|string created_at
 * @property float                                    open
 * @property float                                    close
 * @property float                                    high
 * @property float                                    low
 */
class Transaction extends Eloquent {
	/**
	 * @param      $market
	 * @param      $from_currency
	 * @param      $to_currency
	 * @param bool $excludeIfAll
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public static function whereMarketAndCurrencies($market, $from_currency, $to_currency, $excludeIfAll = false)
	{
		/** @var $query \Illuminate\Database\Query\Builder */
		$query = self::where('from_currency', $from_currency)
			->where('to_currency', $to_currency);

		if ($market === 'all' && $excludeIfAll)
		{
			$query->whereNotIn('market', ['all']);
		}
		else
		{
			$query->where('market', $market);
		}

		return $query;
	}

	/**
	 * @return array
	 */
	public function getDates()
	{
		return array('created_at', 'updated_at', 'made_at');
	}

	/**
	 * Return total amount in source currency
	 *
	 * @return float
	 */
	public function amount()
	{
		return (float) $this->amount_buy + (float) $this->amount_sell;
	}

	/**
	 * Returns total amount in destination currency
	 *
	 * @return float
	 */
	public function in_currency()
	{
		return (float) $this->in_currency_buy + (float) $this->in_currency_sell;
	}
}
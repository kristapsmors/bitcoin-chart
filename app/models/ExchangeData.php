<?php

/**
 * Class ExchangeData
 *
 * @property int                                      id
 * @property string                                   market
 * @property float                                    low
 * @property float                                    high
 * @property float                                    avg
 * @property float                                    volume
 * @property float                                    volume_cur
 * @property string                                   from_currency
 * @property string                                   to_currency
 * @property \Carbon\Carbon|\DateTime|int|null|string datetime
 * @property float                                    open
 * @property float                                    close
 * @property float                                    last
 * @property \Carbon\Carbon|\DateTime|int|string      updated_at
 * @property \Carbon\Carbon|\DateTime|int|string      created_at
 */
class ExchangeData extends Eloquent {
	const MARKET_BITSTAMP      = 'bitstamp';
	const MARKET_BTCE          = 'btc-e';
	const MARKET_MTGOX         = 'mtgox';
	const MARKET_LOCALBITCOINS = 'localbitcoins';
	const MARKET_CAMPBX        = 'campbx';
	const MARKET_KRAKEN        = 'kraken';
	const MARKET_BITCUREX      = 'bitcurex';
	const MARKET_BTCCHINA      = 'btcchina';
	const CURRENCY_USD         = 'USD';
	const CURRENCY_EUR         = 'EUR';
	const CURRENCY_BTC         = 'BTC';
	const CURRENCY_CNY         = 'CNY';
	const CURRENCY_JPY         = 'JPY';
	const CURRENCY_PLN         = 'PLN';
	const CURRENCY_GBP         = 'GBP';
	const CURRENCY_CAD         = 'CAD';
	public static $CURRENCY_PAIRS = [
		[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_USD],
		[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_EUR],
		[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_JPY],
		[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_CNY],
		[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_PLN],
		[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_GBP],
		[ExchangeData::CURRENCY_BTC, ExchangeData::CURRENCY_CAD],
	];
	public static $MARKETS = [
		ExchangeData::MARKET_BITSTAMP,
		ExchangeData::MARKET_BTCE,
		ExchangeData::MARKET_MTGOX,
		ExchangeData::MARKET_LOCALBITCOINS,
		ExchangeData::MARKET_CAMPBX,
		ExchangeData::MARKET_KRAKEN,
		ExchangeData::MARKET_BITCUREX,
		ExchangeData::MARKET_BTCCHINA,
	];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'exchanges_data';

	/**
	 * @param string $market
	 * @param string $from_currency
	 * @param string $to_currency
	 * @param bool   $excludeIfAll
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
	 */
	public static function whereMarketAndCurrencies($market, $from_currency, $to_currency, $excludeIfAll = false)
	{
		$query = self::where('from_currency', $from_currency)
			->where('to_currency', $to_currency);
		if ($excludeIfAll && $market === 'all')
		{
			$query->where('market', '<>', $market);
		}
		else
		{
			$query->where('market', $market);
		}

		return $query;
	}

	/**
	 * @param $from_currency
	 * @param $to_currency
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
	 */
	public static function whereCurrencies($from_currency, $to_currency)
	{
		return self::where('from_currency', $from_currency)
			->where('to_currency', $to_currency);
	}

	public function getDates()
	{
		return array('created_at', 'updated_at', 'datetime');
	}
}
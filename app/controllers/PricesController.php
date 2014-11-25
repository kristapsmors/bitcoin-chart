<?php

class PricesController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function GetShow()
	{
		$from_currency = 'BTC';

		$currencies = [
			['short' => 'USD', 'symbol' => '$', 'long' => 'US Dollar'],
			['short' => 'EUR', 'symbol' => '€', 'long' => 'Euro'],
			['short' => 'CNY', 'symbol' => '¥', 'long' => 'China Yuan Renminbi'],
			['short' => 'JPY', 'symbol' => '¥', 'long' => 'Japan Yen'],
			['short' => 'PLN', 'symbol' => 'zł', 'long' => 'Poland Zloty'],
			['short' => 'GBP', 'symbol' => '£', 'long' => 'United Kingdom Pound'],
			['short' => 'CAD', 'symbol' => '$', 'long' => 'Canada Dollar'],
		];

		$news = News::where('currency', $from_currency)
			->orderBy('datetime')
			->get()
			->map(function (News $news)
			{
				return [
					'id'          => $news->id,
					'title'       => $news->title,
					'link'        => $news->link,
					'datetime'    => $news->datetime,
					'price'       => (float) $news->price,
					'price_24h'   => (float) $news->price_24h,
					'price_3d'    => (float) $news->price_3d,
					'price_week'  => (float) $news->price_week,
					'change_24h'  => $news->getChange(),
					'change_3d'   => $news->getChange('3d'),
					'change_week' => $news->getChange('week'),
					'timestamp'   => $news->datetime->getTimestamp() * 1000,
				];
			})
			->toArray();

		$currency_symbol = '$';

		/** @var $exchanges_usd_data ExchangeData[] */
		$exchanges_usd_data = ExchangeData::where('market', 'all')
			->where('from_currency', $from_currency)
			->where('to_currency', 'USD')
			->orderBy('datetime')
			->get(['avg', 'market', 'datetime']);

		$usd_avg = [
			['name' => 'all', 'id' => 'all', 'data' => [],]
		];
		foreach ($exchanges_usd_data as $row)
		{
			$usd_avg[0]['data'][] = [$row->datetime->getTimestamp() * 1000, (float) $row->avg];
		}

		/** @var $monthData ExchangeSummary[] */
		$monthData = ExchangeSummary::where('market', '<>', 'all')
			->where('type', '1m')
			->where('from_currency', 'BTC')
			->get(['volume', 'market', 'to_currency']);

		$groupedByMarket   = [];
		$groupedByCurrency = [];

		foreach ($monthData as $row)
		{
			if (!isset($groupedByCurrency[$row->to_currency]))
			{
				$groupedByCurrency[$row->to_currency] = 0;
			}
			$marketKey = implode('', [$row->market, $row->to_currency]);
			if (!isset($groupedByMarket[$marketKey]))
			{
				$groupedByMarket[$marketKey] = 0;
			}
			$groupedByCurrency[$row->to_currency] += $row->volume;
			$groupedByMarket[$marketKey] += $row->volume;
		}

		$data_currencies = array_merge([['Currency', 'Amount']], array_map(function ($volume, $currency)
		{
			return [$currency, $volume];
		}, $groupedByCurrency, array_keys($groupedByCurrency)));
		$data_markets    = array_merge([['Market', 'Amount']], array_map(function ($volume, $market)
		{
			return [$market, $volume];
		}, $groupedByMarket, array_keys($groupedByMarket)));

		return View::make('visitor/prices', compact('currencies', 'news', 'currency_symbol', 'usd_avg', 'data_currencies', 'data_markets'));
	}

	public function GetPrices()
	{
		$from_currency = Input::get('from_currency', 'BTC');
		$to_currency   = Input::get('to_currency', 'USD');
		$markets       = ExchangeData::$MARKETS;
		/** @var $data ExchangeData[]|Illuminate\Database\Eloquent\Collection */

		$results = [];
		foreach ($markets as $index => $market)
		{
			$data = ExchangeData::where('market', $market)
				->where('from_currency', $from_currency)
				->where('to_currency', $to_currency)
				->orderBy('datetime')
				->orderBy('id')
				->get();

			if (!$data->count())
			{
				continue;
			}

			$results[$market] = [
				'name'  => $market,
				'data'  => [],
				'index' => $index,
			];

			foreach ($data as $row)
			{
				$hour = strtotime($row->datetime->format('Y-m-d H:i:00'));

				$results[$row->market]['data'][$hour] = [
					$hour * 1000,
					(float) $row->avg,
					//				(float) $row->open,
					//				(float) $row->high,
					//				(float) $row->low,
					//				(float) $row->close,
				];
			}

			$results[$market]['data'] = array_values($results[$market]['data']);
		}

		/** @var $summaryData ExchangeSummary[] */
		$summaryData = ExchangeSummary::where('from_currency', $from_currency)
			->where('to_currency', $to_currency)
			->get();
		$periods     = [
			'24h' => '24 hours',
			'7d'  => '7 days',
			'1m'  => '1 month',
			'3m'  => '3 months',
			'ytd' => 'Year to date',
			'1y'  => '1 year',
			'5y'  => '5 years',
			'all' => 'All'
		];
		$summary     = array_fill_keys(array_keys($periods), ['data' => []]);
		foreach ($summaryData as $row)
		{
			$summary[$row->type]['data'][] = [
				'market' => $row->market,
				'close'  => $row->close,
				'avg'    => $row->avg,
				'low'    => $row->low,
				'high'   => $row->high,
				'volume' => $row->volume_cur,
				'change' => $row->getChange(),
			];
		}

		return Response::json([
			'chart_data' => array_values($results),
			'summary'    => [
				'periods' => $periods,
				'data'    => $summary,
			],
		]);
	}
}
<?php

class NewsController extends BaseController
{

	/**
	 * News Repository
	 *
	 * @var News
	 */
	protected $news;
	protected $currencies = ['BTC' => 'BTC'];

	public function __construct(News $news)
	{
		$this->news = $news;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$news = $this->news->all();

		return View::make('news.index', compact('news'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$currencies = $this->currencies;

		return View::make('news.create', ['currencies' => $currencies]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input      = $this->fillPrices(Input::all());
		$validation = Validator::make($input, News::$rules);

		if ($validation->passes())
		{
			$this->news->create($input);

			return Redirect::route('admin.news.index');
		}

		return Redirect::route('admin.news.create')
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function show($id)
	{
		$news = $this->news->findOrFail($id);

		return View::make('news.show', compact('news'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function edit($id)
	{
		$news = $this->news->find($id);

		if (is_null($news))
		{
			return Redirect::route('admin.news.index');
		}

		$currencies = $this->currencies;

		return View::make('news.edit', compact('news', 'currencies'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function update($id)
	{
		$input      = $this->fillPrices(array_except(Input::all(), '_method'));
		$validation = Validator::make($input, News::$rules);

		if ($validation->passes())
		{
			$news = $this->news->find($id);
			$news->update($input);

			return Redirect::route('admin.news.show', $id);
		}

		return Redirect::route('admin.news.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	protected function fillPrices($input)
	{
		if ($input['datetime'])
		{
			$date   = new \DateTime($input['datetime']);
			$fields = [
				'price'      => null,
				'price_24h'  => 'P1D',
				'price_3d'   => 'P3D',
				'price_week' => 'P7D',
			];
			foreach ($fields as $field => $period)
			{
				if (!$input[$field])
				{
					$searchDate = clone $date;
					if ($period)
					{
						$searchDate->add(new \DateInterval($period));
					}
					$priceRow      = ExchangeData::where('from_currency', $input['currency'])
						->where('to_currency', 'USD')
						->where('market', 'all')
						->orderByRaw(sprintf('ABS(DATEDIFF(datetime, "%s"))', $searchDate->format('Y-m-d H:i:s')))
						->first();
					$input[$field] = $priceRow->avg;
				}
			}
		}

		return $input;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->news->find($id)->delete();

		return Redirect::route('admin.news.index');
	}

}

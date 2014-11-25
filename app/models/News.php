<?php

/**
 * @property string title
 * @property string link
 * @property string currency
 * @property float price
 * @property float price_24h
 * @property float price_3d
 * @property float price_week
 * @property \DateTime|\Carbon\Carbon datetime
 * @property int id
 */
class News extends Eloquent
{
	protected $table = 'news_items';

	protected $guarded = array();

	public static $rules = array(
		'datetime' => 'required',
		'currency' => 'required',
		'title'    => 'required',
		'link'     => 'required',
		'price'    => 'required',
	);

	public function getChange($period = '24h')
	{
		if (!(float)$this->price)
		{
			return 0;
		}
		$end  = $this->{"price_{$period}"};
		$diff = ($end / $this->price - 1) * 100;

		return $diff;
	}

	public function getDates()
	{
		return array('created_at', 'updated_at', 'datetime');
	}
}

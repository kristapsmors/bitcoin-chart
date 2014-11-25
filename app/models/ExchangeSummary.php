<?php

/**
 * Class ExchangeData
 *
 * @property string market
 * @property string type
 * @property float  low
 * @property float  high
 * @property float  avg
 * @property float  volume
 * @property mixed  volume_cur
 * @property string from_currency
 * @property string to_currency
 * @property float  open
 * @property float  close
 * @property float  last
 */
class ExchangeSummary extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'exchanges_summary';

	public function getChange()
	{
		if (!(float)$this->open)
		{
			return 0;
		}

		return (($this->close / $this->open) - 1) * 100;
	}
}
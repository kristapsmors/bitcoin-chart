<?php

use Illuminate\Console\Command;

class ExchangeTransactionsCommandBase extends Command
{
	protected $aggregate = [];
	protected $lastId = [];

	protected function aggregate($market, $from_currency, $to_currency, $date, $amount, $price, $tid, $sell = true)
	{
		$lastId = $this->getLastId($market, $from_currency, $to_currency);

		if ($tid <= $lastId)
		{
			return;
		}

		$id = sha1(implode(':', [$market, $from_currency, $to_currency, $date]));

		if (!isset($this->aggregate[$id]))
		{
			$this->aggregate[$id] = [
				'id'                  => $id,
				'market'              => $market,
				'from_currency'       => $from_currency,
				'to_currency'         => $to_currency,
				'made_at'             => $date,
				'in_currency_buy'     => 0.0,
				'in_currency_sell'    => 0.0,
				'amount_buy'          => 0.0,
				'amount_sell'         => 0.0,
				'count_buy'           => 0,
				'count_sell'          => 0,
				'open'                => $price,
				'close'               => 0.0,
				'high'                => 0.0,
				'low'                 => PHP_INT_MAX,
				'last_transaction_id' => $lastId,
				'created_at'          => date('Y-m-d H:i:s'),
				'updated_at'          => date('Y-m-d H:i:s'),
			];
		}
		$suffix = $sell ? 'sell' : 'buy';

		$in_currency = $amount * $price;
		$this->aggregate[$id]["in_currency_{$suffix}"] += $in_currency;
		$this->aggregate[$id]["amount_{$suffix}"] += $amount;
		$this->aggregate[$id]["count_{$suffix}"]++;
		$this->aggregate[$id]['last_transaction_id'] = $tid;
		$this->aggregate[$id]['close']               = $price;
		if ($price > $this->aggregate[$id]['high'])
		{
			$this->aggregate[$id]['high'] = $price;
		}
		if ($price < $this->aggregate[$id]['low'])
		{
			$this->aggregate[$id]['low'] = $price;
		}

		$this->setLastId($tid, $market, $from_currency, $to_currency);
	}

	protected function flush()
	{
		$query = null;
		foreach ($this->aggregate as $row)
		{
			$updateFields = ['in_currency_buy', 'in_currency_sell', 'amount_buy', 'amount_sell',
							 'count_buy', 'count_sell', 'last_transaction_id', 'updated_at',
							 'close', 'high', 'low'];

			$rowValues = array_values($row);
			if (!$query)
			{
				$keys   = array_keys($row);
				$fields = sprintf('`%s`', implode('`, `', array_keys($row)));
				$values = implode(', ', array_fill(0, count($keys), '?'));
				$update = implode(', ', array_map(function ($field)
				{
					switch ($field)
					{
						case 'last_transaction_id':
						case 'updated_at':
							return "`{$field}` = ?";
						case 'high':
							return "`{$field}` = GREATEST(`{$field}`, ?)";
						case 'low':
							return "`{$field}` = LEAST(`{$field}`, ?)";
						default:
							return "`{$field}` = `{$field}` + ?";
					}
				}, $updateFields));

				$query = sprintf('INSERT INTO `transactions` (%s) VALUES(%s) ON DUPLICATE KEY UPDATE %s',
					$fields,
					$values,
					$update);
			}
			foreach ($updateFields as $field)
			{
				$rowValues[] = $row[$field];
			}

			DB::statement($query, $rowValues);
		}
		$this->aggregate = [];

		return true;
	}

	protected function getLastId($market, $from_currency, $to_currency)
	{
		$key = implode(':', [$market, $from_currency, $to_currency]);
		if (!isset($this->lastId[$key]))
		{
			$this->lastId[$key] = ($last = Transaction::whereMarketAndCurrencies($market, $from_currency, $to_currency)
				->orderBy('last_transaction_id', 'desc')
				->take(1)
				->get(['last_transaction_id'])
				->first()) ? $last->last_transaction_id : 0;
		}

		return $this->lastId[$key];
	}

	protected function setLastId($tid, $market, $from_currency, $to_currency)
	{
		$key = implode(':', [$market, $from_currency, $to_currency]);

		$this->lastId[$key] = $tid;
	}

	protected function isLocked()
	{
		$lockFile = $this->getLockFile();

		return file_exists($lockFile) && (time() - filemtime($lockFile)) < $this->getLockTime();
	}

	protected function lock()
	{
		touch($this->getLockFile());
	}

	protected function unlock()
	{
		$lockFile = $this->getLockFile();
		if (file_exists($lockFile))
		{
			unlink($lockFile);
		}
	}

	protected function getLockFile()
	{

		return '/tmp/bitcoin-charts-' . get_class($this) . '.lock';
	}

	protected function getLockTime()
	{
		return 600; // 10 minutes
	}
}

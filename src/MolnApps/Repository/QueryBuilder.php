<?php

namespace MolnApps\Repository;

class QueryBuilder
{
	private $columns = [];
	private $where = [];
	private $order = [];
	private $limit = 0;
	private $offset = 0;

	public function columns(array $columns)
	{
		$this->columns = $columns;

		return $this;
	}

	public function where(array $where)
	{
		$this->where = array_merge($this->where, $where);

		return $this;
	}

	public function orderBy(array $order)
	{
		$this->order = $order;

		return $this;
	}

	public function limit($limit)
	{
		$this->limit = (int)$limit;

		return $this;
	}

	public function page($page)
	{
		if ( ! $this->limit) {
			throw new \Exception('Please provide a limit first.');
		}

		$this->offset = $this->limit * ((int)$page - 1);

		return $this;
	}

	public function offset($offset)
	{
		$this->offset = (int)$offset;

		return $this;
	}

	public function toArray()
	{
		$result = [];

		if ($this->columns) {
			$result['columns'] = $this->columns;
		}

		if ($this->where) {
			$result['where'] = $this->where;
		}

		if ($this->order) {
			$result['order'] = $this->order;
		}

		if ($this->limit) {
			$result['limit'] = $this->limit;
		}

		if ($this->offset) {
			$result['offset'] = $this->offset;
		}

		return $result;
	}
}
<?php

namespace MolnApps\Repository\Contracts;

use \MolnApps\Repository\QueryBuilder;

interface Table
{
	public function select(array $query);
	public function insert($assignments);
	public function update($assignments, $where);
	public function delete($where);

	public function executeSelect(QueryBuilder $builder);
}
<?php

namespace MolnApps\Repository\Contracts;

use \MolnApps\Repository\QueryBuilder;

interface Scope
{
	public function apply(QueryBuilder $queryBuilder);
}
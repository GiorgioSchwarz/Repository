<?php

namespace MolnApps\Repository\Traits;

use \MolnApps\Repository\QueryBuilder as QueryBuilderImplementation;

trait QueryBuilder
{
	protected function getQueryBuilderMethods()
	{
		return ['columns', 'where', 'orderBy', 'limit', 'offset', 'page'];
	}

	public function getQueryBuilder()
	{
		if ( ! $this->queryBuilder) {
			$this->setQueryBuilder();
		}

		return $this->queryBuilder;
	}

	public function setQueryBuilder(QueryBuilderImplementation $queryBuilder = null)
	{
		$this->queryBuilder = ($queryBuilder) ?: $this->createQueryBuilder();

		return $this;
	}

	public function createQueryBuilder()
	{
		return new QueryBuilderImplementation;
	}

	private function resetQueryBuilder()
	{
		$this->queryBuilder = null;
	}
}
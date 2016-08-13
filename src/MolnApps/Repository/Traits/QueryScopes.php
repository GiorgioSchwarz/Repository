<?php

namespace MolnApps\Repository\Traits;

use \MolnApps\Repository\QueryBuilder;
use \MolnApps\Repository\Contracts\Scope;

trait QueryScopes
{
	private $scopes = [];

	public function addScope($name, Scope $populator)
	{
		$this->scopes[$name] = $populator;

		return $this;
	}

	public function removeScope($name)
	{
		unset($this->scopes[$name]);

		return $this;
	}

	public function hasScope($name)
	{
		return isset($this->scopes[$name]);
	}

	protected function applyScope(QueryBuilder $queryBuilder)
	{
		foreach ($this->scopes as $scope) {
			$scope->apply($queryBuilder);
		}

		return true;
	}
}
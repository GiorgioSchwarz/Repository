<?php

namespace MolnApps\Repository\Traits;

use \MolnApps\Repository\QueryBuilder;
use \MolnApps\Repository\Contracts\Scope;

trait QueryScopes
{
	private $scopes = [];

	private $ignore = [];

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

	public function withoutScope($scope)
	{
		return $this->withoutScopes([$scope]);
	}

	public function withoutScopes(array $scopes)
	{
		$this->ignore = array_merge($this->ignore, $scopes);

		return $this;
	}

	private function resetIgnoredScopes()
	{
		$this->ignore = [];
	}

	protected function applyScope(QueryBuilder $queryBuilder)
	{
		foreach ($this->scopes as $name => $scope) {
			if ( ! in_array($name, $this->ignore)) {
				$scope->apply($queryBuilder);
			}
		}

		$this->resetIgnoredScopes();

		return true;
	}
}
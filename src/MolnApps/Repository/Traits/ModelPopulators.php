<?php

namespace MolnApps\Repository\Traits;

use \MolnApps\Repository\Contracts\Model;
use \MolnApps\Repository\Contracts\Populator;

trait ModelPopulators
{
	private $populators = [];

	public function addPopulator($name, Populator $populator)
	{
		$this->populators[$name] = $populator;

		return $this;
	}

	public function removePopulator($name)
	{
		unset($this->populators[$name]);

		return $this;
	}

	public function hasPopulator($name)
	{
		return isset($this->populators[$name]);
	}

	protected function populate(Model $model, $operation)
	{
		foreach ($this->populators as $populator) {
			$populator->populate($model, $operation);
		}

		return true;
	}
}
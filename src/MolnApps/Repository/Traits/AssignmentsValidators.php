<?php

namespace MolnApps\Repository\Traits;

use \MolnApps\Repository\Contracts\Validator;
use \MolnApps\Repository\Contracts\Model;

trait AssignmentsValidators
{
	private $validators = [];

	public function addValidator($name, Validator $validator)
	{
		$this->validators[$name] = $validator;

		return $this;
	}

	public function removeValidator($name)
	{
		unset($this->validators[$name]);

		return $this;
	}

	public function hasValidator($name)
	{
		return isset($this->validators[$name]);
	}

	protected function validate(Model $model, $operation)
	{
		foreach ($this->validators as $validator) {
			if ( ! $validator->validate($model, $operation)) {
				return false;
			}
		}

		return true;
	}
}
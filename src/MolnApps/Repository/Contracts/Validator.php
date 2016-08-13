<?php

namespace MolnApps\Repository\Contracts;

interface Validator
{
	public function validate(Model $model, $operation);
}
<?php

namespace MolnApps\Repository\Contracts;

interface Middleware
{
	public function authorize(Model $model, $operation);
}
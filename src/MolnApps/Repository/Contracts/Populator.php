<?php

namespace MolnApps\Repository\Contracts;

interface Populator
{
	public function populate(Model $model, $operation);
}
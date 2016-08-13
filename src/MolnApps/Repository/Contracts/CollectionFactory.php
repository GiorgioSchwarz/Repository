<?php

namespace MolnApps\Repository\Contracts;

interface CollectionFactory
{
	public function createCollection(array $rows);
}
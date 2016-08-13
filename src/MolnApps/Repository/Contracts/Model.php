<?php

namespace MolnApps\Repository\Contracts;

interface Model
{
	public function isNew();
	public function isLocked();

	public function getAssignments($operation);
	public function getIdentity();

	public function setAssignment($name, $value);
	public function getAssignment($name);
}
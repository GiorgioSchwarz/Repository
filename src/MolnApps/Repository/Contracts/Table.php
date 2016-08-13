<?php

namespace MolnApps\Repository\Contracts;

interface Table
{
	public function select(array $query);
	public function insert($assignments);
	public function update($assignments, $where);
	public function delete($where);
}
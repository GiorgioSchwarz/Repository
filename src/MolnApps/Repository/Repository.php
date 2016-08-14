<?php

namespace MolnApps\Repository;

use \MolnApps\Repository\Contracts\Table;
use \MolnApps\Repository\Contracts\CollectionFactory;
use \MolnApps\Repository\Contracts\Model;

use \MolnApps\Repository\Traits\ModelMiddlewares;
use \MolnApps\Repository\Traits\ModelPopulators;
use \MolnApps\Repository\Traits\ModelValidators;
use \MolnApps\Repository\Traits\QueryScopes;
use \MolnApps\Repository\Traits\QueryBuilder as QueryBuilderTrait;

class Repository
{
	use ModelMiddlewares;
	use ModelPopulators;
	use ModelValidators;
	use QueryScopes;
	use QueryBuilderTrait;

	private $table;
	private $collectionFactory;

	private $queryBuilder;

	public function __construct(Table $table, CollectionFactory $collectionFactory)
	{
		$this->table = $table;
		$this->collectionFactory = $collectionFactory;
	}

	public function __call($name, $args)
	{
		if (in_array($name, $this->getQueryBuilderMethods())) {
			return $this->invokeQueryBuilderMethod($name, $args);
		}
	}

	private function invokeQueryBuilderMethod($name, $args)
	{
		call_user_func_array([$this->getQueryBuilder(), $name], $args);
		return $this;
	}

	public function where(array $where)
	{
		return $this->invokeQueryBuilderMethod('where', [$where]);
	}

	public function find()
	{
		$this->applyScope($this->getQueryBuilder());

		$rows = $this->table->executeSelect($this->getQueryBuilder());

		$this->resetQueryBuilder();

		return $this->collectionFactory->createCollection($rows);
	}

	public function save(Model $model)
	{
		if ($model->isNew()) {
			return $this->insert($model);
		} else {
			return $this->update($model);
		}
	}

	private function insert(Model $model)
	{
		if ( 
			! $this->populate($model, 'insert') || 
			! $this->validate($model, 'insert') || 
			! $this->authorize($model, 'insert')
		) {
			return;
		}

		$this->table->insert($model->getAssignments('insert'));
	}

	private function update(Model $model)
	{
		$identity = $model->getIdentity();

		$this->guardIdentity($identity);

		if ( 
			! $this->populate($model, 'update') ||
			! $this->validate($model, 'update') ||
			! $this->authorize($model, 'update')
		) {
			return;
		}

		$this->table->update($model->getAssignments('update'), $identity);
	}

	public function delete(Model $model)
	{
		$identity = $model->getIdentity();

		$this->guardExistance($model);
		$this->guardDeletable($model);
		$this->guardIdentity($identity);

		if ( ! $this->authorize($model, 'delete')) {
			return;
		}

		$this->table->delete($identity);
	}

	protected function guardDeletable(Model $model)
	{
		if ($model->isLocked()) {
			throw new \Exception('You cannot delete this model');
		}
	}

	protected function guardExistance(Model $model)
	{
		if ($model->isNew()) {
			throw new \Exception('You cannot delete a new model');
		}
	}

	protected function guardIdentity($identity)
	{
		if ( ! $identity) {
			throw new \Exception('Please provide an identity for this model');
		}
	}
}
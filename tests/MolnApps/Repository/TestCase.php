<?php

namespace MolnApps\Repository;

use \MolnApps\Repository\Contracts\Table;
use \MolnApps\Repository\Contracts\CollectionFactory;
use \MolnApps\Repository\Contracts\Model;

use \MolnApps\Repository\Contracts\Middleware;
use \MolnApps\Repository\Contracts\Validator;
use \MolnApps\Repository\Contracts\Populator;
use \MolnApps\Repository\Contracts\Scope;

class TestCase extends \PHPUnit_Framework_TestCase
{
	protected $table;
	protected $model;
	protected $repository;

	protected $assignments = [];

	private $queryBuilder;

	protected function setUp()
	{
		$this->table = $this->createMock(Table::class);
		$this->collectionFactory = $this->createMock(CollectionFactory::class);

		$this->model = $this->createMock(Model::class);

		$this->assignments = [];

		$this->repository = new Repository($this->table, $this->collectionFactory);
	}

	// ! Model methods

	protected function newModel(array $assignments)
	{
		$this->modelIsNew();
		$this->modelInsertAssignments($assignments);
	}

	protected function existingModel(array $assignments, array $identity)
	{
		$this->modelExists();
		$this->modelUpdateAssignments($assignments);
		$this->modelIdentity($identity);
	}

	protected function deletableModel(array $identity)
	{
		$this->modelExists();
		$this->modelIsDeletable();
		$this->modelIdentity($identity);
	}

	protected function lockedModel(array $identity)
	{
		$this->modelExists();
		$this->modelIsLocked();
		$this->modelIdentity($identity);
	}

	protected function modelIsNew($isNew = true)
	{
		$this->modelExpects('isNew', $isNew);
	}

	protected function modelExists()
	{
		return $this->modelIsNew(false);
	}

	protected function modelIsLocked($isLocked = true)
	{
		$this->modelExpects('isLocked', $isLocked);
	}

	protected function modelIsDeletable()
	{
		return $this->modelIsLocked(false);
	}

	protected function modelInsertAssignments(array $assignments)
	{
		$this->addAssignments($assignments);
		
		$this->modelExpects('getAssignments', function() {
			return $this->assignments;
		});
	}

	protected function modelUpdateAssignments(array $assignments)
	{
		$this->addAssignments($assignments);

		$this->modelExpects('getAssignments', function() {
			return $this->assignments;
		});
	}

	protected function modelIdentity(array $identity)
	{
		$this->modelExpects('getIdentity', $identity);
	}

	protected function modelExpects($method, $willReturn)
	{
		$willReturn = ($willReturn instanceof \Closure) 
			? $this->returnCallback($willReturn)
			: $this->returnValue($willReturn);

		$this->model->method($method)->will($willReturn);
	}

	// ! Table methods

	protected function tableShouldReceiveExecuteSelect(array $returnsRows = []) {
		$this->repositoryUsesQueryBuilder();

		$this->tableExpects('executeSelect')->with($this->queryBuilder)->willReturn($returnsRows);
	}

	protected function tableShouldReceiveInsert(array $assignments)
	{
		$this->tableExpects('insert')->with($assignments);
	}

	protected function tableShouldNotReceiveInsert()
	{
		$this->tableExpects('insert', false);
	}

	protected function tableShouldReceiveUpdate(array $assignments, array $identity)
	{
		$this->tableExpects('update')->with($assignments, $identity);
	}

	protected function tableShouldNotReceiveUpdate()
	{
		$this->tableExpects('update', false);
	}

	protected function tableShouldReceiveDelete(array $identity)
	{
		$this->tableExpects('delete')->with($identity);
	}

	protected function tableShouldNotReceiveDelete()
	{
		$this->tableExpects('delete', false);
	}

	private function tableExpects($method, $expected = true)
	{
		$expected = ($expected) ? $this->once() : $this->never();
		return $this->table->expects($expected)->method($method);
	}

	// ! Middleware methods

	protected function newMiddleware($operation, $willReturn)
	{
		$middleware = $this->createMock(Middleware::class);

		$middleware
			->expects($this->any())
			->method('authorize')
			->with($this->model, $operation)
			->willReturn($willReturn);

		return $middleware;
	}

	// ! Populator methods

	protected function newPopulator($operation, array $additionalAssignments)
	{
		$this->addAssignments($additionalAssignments);

		$populator = $this->createMock(Populator::class);
		
		$populator
			->expects($this->once())
			->method('populate')
			->with($this->model, $operation);

		return $populator;
	}

	// ! Validator methods

	protected function newValidator($operation, $willReturn)
	{
		$validator = $this->createMock(Validator::class);

		$validator
			->expects($this->once())
			->method('validate')
			->with($this->model, $operation)
			->willReturn($willReturn);

		return $validator;
	}

	// ! Assignments methods

	protected function addAssignments(array $assignments)
	{
		$this->assignments = array_merge($this->assignments, $assignments);
	}

	// ! Collection factory method

	protected function collectionFactoryShouldReturn(array $rows)
	{
		$this->collectionFactory->method('createCollection')->willReturn((object)$rows);
	}

	// ! Scope methods

	protected function createScope($where)
	{
		$scope = $this->createMock(Scope::class);
		
		$scope->method('apply')->will(
			$this->returnCallback(
				function(QueryBuilder $queryBuilder) use ($where) {
					$queryBuilder->where($where);
				}
			)
		);

		return $scope;
	}

	// ! Repository and QueryBuilder methods

	protected function repositoryUsesQueryBuilder(QueryBuilder $queryBuilder = null)
	{
		$this->repository->setQueryBuilder($queryBuilder);

		$this->queryBuilder = $this->repository->getQueryBuilder();
	}

	protected function createQueryBuilder()
	{
		return $this->repository->createQueryBuilder();
	}

	protected function assertQueryBuilder($expectedArray)
	{
		$this->assertEquals($expectedArray, $this->queryBuilder->toArray());
	}

	protected function assertQueryBuilderWasReset()
	{
		$this->assertNotEquals($this->queryBuilder, $this->repository->getQueryBuilder());
	}
}
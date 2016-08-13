<?php

namespace MolnApps\Repository;

use \MolnApps\Repository\Contracts\Scope;

class RepositoryScopeTest extends TestCase
{
	/** @test */
	public function it_binds_a_scope_and_returns_self()
	{
		$scope = $this->createMock(Scope::class);
		
		$returnValue = $this->repository->addScope('account', $scope);

		$this->assertEquals($this->repository, $returnValue);
	}

	/** @test */
	public function it_binds_a_scope()
	{
		$scope = $this->createMock(Scope::class);
		
		$this->repository->addScope('account', $scope);

		$result = $this->repository->hasScope('account');
		$this->assertTrue($result);
	}

	/** @test */
	public function it_removes_a_scope()
	{
		$scope = $this->createMock(Scope::class);
		
		$this->repository->addScope('account', $scope);
		$result = $this->repository->hasScope('account');
		$this->assertTrue($result);

		$this->repository->removeScope('account');
		$result = $this->repository->hasScope('account');
		$this->assertFalse($result);
	}

	/** @test */
	public function it_applies_a_scope_to_query()
	{
		$accountScope = $this->createMock(Scope::class);
		
		$accountScope->method('apply')->will($this->returnCallback(function(QueryBuilder $queryBuilder){
			$queryBuilder->where(['accountId' => 12]);
		}));
		
		$this->repository->addScope('account', $accountScope);

		$this->tableShouldReceiveSelect(['where' => ['foo' => 'bar', 'accountId' => 12]], []);

		$this->repository->where(['foo' => 'bar'])->find();
	}

	/** @test */
	public function it_applies_multiple_scopes_to_query()
	{
		$accountScope = $this->createMock(Scope::class);
		
		$accountScope->method('apply')->will($this->returnCallback(function(QueryBuilder $queryBuilder){
			$queryBuilder->where(['accountId' => 12]);
		}));

		$areaScope = $this->createMock(Scope::class);
		
		$areaIdsQuery = new QueryBuilder();
			$areaIdsQuery
				->columns(['areaId'])
				->where(['userId' => 5]);

		$areaScope
			->method('apply')
			->will($this->returnCallback(
				function(QueryBuilder $queryBuilder) use ($areaIdsQuery) {
					$queryBuilder->where([['areaId', 'in', $areaIdsQuery]]);
				}
			));
		
		$this->repository->addScope('account', $accountScope);
		$this->repository->addScope('user', $areaScope);

		$this->tableShouldReceiveSelect([
			'where' => [
				'foo' => 'bar', 
				'accountId' => 12, 
				['areaId', 'in', $areaIdsQuery]
			]
		], []);

		$this->repository->where(['foo' => 'bar'])->find();
	}
}
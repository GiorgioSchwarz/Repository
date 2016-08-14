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
		$accountScope = $this->createScope(['accountId' => 12]);
		$this->repository->addScope('account', $accountScope);

		$this->tableShouldReceiveExecuteSelect();

		$this->repository->where(['foo' => 'bar'])->find();

		$this->assertQueryBuilder(['where' => ['foo' => 'bar', 'accountId' => 12]]);
	}

	/** @test */
	public function it_applies_multiple_scopes_to_query()
	{
		$accountScope = $this->createScope(['accountId' => 12]);

		$areaIdsQuery = $this->getAreaIdsQuery();
		
		$areaScope = $this->createScope([['areaId', 'in', $areaIdsQuery]]);
		
		$this->repository->addScope('account', $accountScope);
		$this->repository->addScope('area', $areaScope);

		$this->tableShouldReceiveExecuteSelect();

		$this->repository->where(['foo' => 'bar'])->find();

		$this->assertQueryBuilder(
			['where' => ['foo' => 'bar', 'accountId' => 12, ['areaId', 'in', $areaIdsQuery]]]
		);
	}

	/** @test */
	public function it_ignores_a_scope()
	{
		$accountScope = $this->createScope(['accountId' => 12]);
		$userScope = $this->createScope(['userId' => 5]);

		$this->repository->addScope('account', $accountScope);
		$this->repository->addScope('user', $userScope);

		$this->tableShouldReceiveExecuteSelect();

		$this->repository->withoutScope('account')->where(['foo' => 'bar'])->find();

		$this->assertQueryBuilder(['where' => ['foo' => 'bar', 'userId' => 5]]);
	}

	/** @test */
	public function it_ignores_multiple_scopes()
	{
		$accountScope = $this->createScope(['accountId' => 12]);
		$userScope = $this->createScope(['userId' => 5]);
		
		$this->repository->addScope('account', $accountScope);
		$this->repository->addScope('user', $userScope);

		$this->tableShouldReceiveExecuteSelect();

		$this->repository->withoutScopes(['account', 'user'])->where(['foo' => 'bar'])->find();

		$this->assertQueryBuilder(['where' => ['foo' => 'bar']]);
	}

	/** @test */
	public function it_resets_ignored_scopes_after_the_query_was_run()
	{
		$accountScope = $this->createScope(['accountId' => 12]);
		$userScope = $this->createScope(['userId' => 5]);
		
		$this->repository->addScope('account', $accountScope);
		$this->repository->addScope('user', $userScope);

		$queryBuilder1 = $this->createQueryBuilder();
		$queryBuilder2 = $this->createQueryBuilder();

		$this->table
			->method('executeSelect')
			->withConsecutive([$queryBuilder1], [$queryBuilder2])
			->willReturn([]);

		// Execute query 1
		$this->repositoryUsesQueryBuilder($queryBuilder1);

		$this->repository->withoutScopes(['account', 'user'])->where(['foo' => 'bar'])->find();

		$this->assertQueryBuilder(['where' => ['foo' => 'bar']]);

		// Execute query 2
		$this->repositoryUsesQueryBuilder($queryBuilder2);
		
		$this->repository->where(['foo' => 'bar'])->find();

		$this->assertQueryBuilder(['where' => ['foo' => 'bar', 'accountId' => 12, 'userId' => 5]]);
	}

	private function getAreaIdsQuery()
	{
		$areaIdsQuery = new QueryBuilder();
		
		$areaIdsQuery
			->columns(['areaId'])
			->where(['userId' => 5]);

		return $areaIdsQuery;
	}
}
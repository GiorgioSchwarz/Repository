<?php

namespace MolnApps\Repository;

use \MolnApps\Repository\Contracts\Populator;

class RepositoryPopulatorTest extends TestCase
{
	/** @test */
	public function it_binds_a_populator_and_returns_self()
	{
		$populator = $this->createMock(Populator::class);
		
		$returnValue = $this->repository->addPopulator('setAccountId', $populator);
		$this->assertEquals($this->repository, $returnValue);
	}

	/** @test */
	public function it_binds_a_populator()
	{
		$populator = $this->createMock(Populator::class);
		
		$this->repository->addPopulator('setAccountId', $populator);

		$result = $this->repository->hasPopulator('setAccountId');
		$this->assertTrue($result);
	}

	/** @test */
	public function it_removes_a_populator()
	{
		$populator = $this->createMock(Populator::class);
		
		$this->repository->addPopulator('setAccountId', $populator);
		$result = $this->repository->hasPopulator('setAccountId');
		$this->assertTrue($result);

		$this->repository->removePopulator('setAccountId');
		$result = $this->repository->hasPopulator('setAccountId');
		$this->assertFalse($result);
	}

	/** @test */
	public function it_inserts_new_model_with_one_populator()
	{
		$this->newModel(['foo' => 'bar']);
		
		$populator = $this->newPopulator('insert', ['accountId' => 12]);
		$this->repository->addPopulator('setAccountId', $populator);

		$this->tableShouldReceiveInsert(['foo' => 'bar', 'accountId' => 12]);

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_inserts_new_model_with_multiple_populators()
	{
		$this->newModel(['foo' => 'bar']);

		$populator1 = $this->newPopulator('insert', ['accountId' => 12]);
		$populator2 = $this->newPopulator('insert', ['createdAt' => '2015-02-18 01:02:03']);

		$this->repository
			->addPopulator('setAccountId', $populator1)
			->addPopulator('setTimestamps', $populator2);

		$this->tableShouldReceiveInsert([
			'foo' => 'bar', 
			'accountId' => 12, 
			'createdAt' => '2015-02-18 01:02:03'
		]);

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_updates_existing_model_with_one_populator()
	{
		$this->existingModel(['foo' => 'bar'], ['id' => 12]);

		$populator = $this->newPopulator('update', ['updatedAt' => '2015-02-18 01:02:03']);
		$this->repository->addPopulator('setTimestamps', $populator);

		$this->tableShouldReceiveUpdate([
			'foo' => 'bar', 
			'updatedAt' => '2015-02-18 01:02:03'
		], ['id' => 12]);

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_updates_existing_model_with_multiple_populators()
	{
		$this->existingModel(['foo' => 'bar'], ['id' => 12]);

		$populator1 = $this->newPopulator('update', ['updatedAt' => '2015-02-18 01:02:03']);
		$populator2 = $this->newPopulator('update', ['editorUserId' => 13]);

		$this->repository
			->addPopulator('setTimestamps', $populator1)
			->addPopulator('setEditorUserId', $populator2);

		$this->tableShouldReceiveUpdate([
			'foo' => 'bar', 
			'updatedAt' => '2015-02-18 01:02:03', 
			'editorUserId' => 13
		], ['id' => 12]);

		$this->repository->save($this->model);
	}
}
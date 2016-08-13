<?php

namespace MolnApps\Repository;

use \MolnApps\Repository\Contracts\Validator;

class RepositoryValidatorTest extends TestCase
{
	/** @test */
	public function it_binds_a_validator_and_returns_self()
	{
		$validator = $this->createMock(Validator::class);
		
		$returnValue = $this->repository->addValidator('ReportValidator', $validator);

		$this->assertEquals($this->repository, $returnValue);
	}

	/** @test */
	public function it_binds_a_validator()
	{
		$validator = $this->createMock(Validator::class);
		
		$this->repository->addValidator('ReportValidator', $validator);

		$result = $this->repository->hasValidator('ReportValidator');
		$this->assertTrue($result);
	}

	/** @test */
	public function it_removes_a_validator()
	{
		$validator = $this->createMock(Validator::class);
		
		$this->repository->addValidator('ReportValidator', $validator);
		$result = $this->repository->hasValidator('ReportValidator');
		$this->assertTrue($result);

		$this->repository->removeValidator('ReportValidator');
		$result = $this->repository->hasValidator('ReportValidator');
		$this->assertFalse($result);
	}

	/** @test */
	public function it_inserts_new_model_if_valid()
	{
		$this->newModel(['foo' => 'bar']);
		
		$validator = $this->newValidator('insert', true);
		$this->repository->addValidator('BaseValidator', $validator);

		$this->tableShouldReceiveInsert(['foo' => 'bar']);

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_does_not_insert_new_model_if_not_valid()
	{
		$this->newModel(['foo' => 'bar']);
		
		$validator = $this->newValidator('insert', false);
		$this->repository->addValidator('BaseValidator', $validator);

		$this->tableShouldNotReceiveInsert();

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_updates_existing_model_if_valid()
	{
		$this->existingModel(['foo' => 'bar'], ['id' => 12]);
		
		$validator = $this->newValidator('update', true);
		$this->repository->addValidator('BaseValidator', $validator);

		$this->tableShouldReceiveUpdate(['foo' => 'bar'], ['id' => 12]);

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_wont_update_existing_model_if_invalid()
	{
		$this->existingModel(['foo' => 'bar'], ['id' => 12]);
		
		$validator = $this->newValidator('update', false);
		$this->repository->addValidator('BaseValidator', $validator);

		$this->tableShouldNotReceiveUpdate();

		$this->repository->save($this->model);
	}
}
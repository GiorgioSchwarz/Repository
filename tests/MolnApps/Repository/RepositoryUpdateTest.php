<?php

namespace MolnApps\Repository;

class RepositoryUpdateTest extends TestCase
{
	/** @test */
	public function it_inserts_new_model()
	{
		$this->newModel(['foo' => 'bar']);
		
		$this->tableShouldReceiveInsert(['foo' => 'bar']);

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_updates_existing_model()
	{
		$this->existingModel(['foo' => 'bar'], ['id' => 12]);
		
		$this->tableShouldReceiveUpdate(['foo' => 'bar'], ['id' => 12]);

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_throws_if_update_is_called_but_model_does_not_return_any_identity()
	{
		$this->existingModel(['foo' => 'bar'], []);
		
		$this->setExpectedException(\Exception::class, 'Please provide an identity for this model');

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_deletes_existing_model_which_is_deletable_and_provides_identity()
	{
		$this->deletableModel(['id' => 12]);
		
		$this->tableShouldReceiveDelete(['id' => 12]);

		$this->repository->delete($this->model);
	}

	/** @test */
	public function it_throws_if_delete_is_called_on_new_model()
	{
		$this->newModel([]);
		
		$this->setExpectedException(\Exception::class, 'You cannot delete a new model');

		$this->repository->delete($this->model);
	}

	/** @test */
	public function it_throws_if_delete_is_called_on_locked_model()
	{
		$this->lockedModel(['id' => 12]);
		
		$this->setExpectedException(\Exception::class, 'You cannot delete this model');

		$this->repository->delete($this->model);
	}

	/** @test */
	public function it_throws_if_delete_is_called_but_model_does_not_return_any_identity()
	{
		$this->deletableModel([]);
		
		$this->setExpectedException(\Exception::class, 'Please provide an identity for this model');

		$this->repository->delete($this->model);
	}
}
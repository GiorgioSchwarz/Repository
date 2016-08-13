<?php

namespace MolnApps\Repository;

use \MolnApps\Repository\Contracts\Middleware;

class RepositoryMiddlewareTest extends TestCase
{
	/** @test */
	public function it_binds_a_middleware_and_returns_self()
	{
		$middleware = $this->createMock(Middleware::class);
		
		$returnValue = $this->repository->addMiddleware('CanUpdateRecord', $middleware);

		$this->assertEquals($this->repository, $returnValue);
	}

	/** @test */
	public function it_binds_a_middleware()
	{
		$middleware = $this->createMock(Middleware::class);
		
		$this->repository->addMiddleware('CanUpdateRecord', $middleware);

		$result = $this->repository->hasMiddleware('CanUpdateRecord');

		$this->assertTrue($result);
	}

	/** @test */
	public function it_removes_a_middleware()
	{
		$middleware = $this->createMock(Middleware::class);
		
		$this->repository->addMiddleware('CanUpdateRecord', $middleware);

		$result = $this->repository->hasMiddleware('CanUpdateRecord');

		$this->assertTrue($result);

		$this->repository->removeMiddleware('CanUpdateRecord');

		$result = $this->repository->hasMiddleware('CanUpdateRecord');

		$this->assertFalse($result);
	}

	/** @test */
	public function it_inserts_new_model_with_one_middleware()
	{
		$this->newModel(['foo' => 'bar']);

		$middleware = $this->newMiddleware('insert', true);
		$this->repository->addMiddleware('CanUpdateRecord', $middleware);

		$this->tableShouldReceiveInsert(['foo' => 'bar']);

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_inserts_new_model_with_multiple_middlewares()
	{
		$this->newModel(['foo' => 'bar']);

		$middleware1 = $this->newMiddleware('insert', true);
		$middleware2 = $this->newMiddleware('insert', true);

		$this->repository
			->addMiddleware('CanUpdateRecord', $middleware1)
			->addMiddleware('CanAccessRecordCategory', $middleware2);

		$this->tableShouldReceiveInsert(['foo' => 'bar']);

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_wont_insert_new_model_with_middleware_error()
	{
		$this->newModel(['foo' => 'bar']);

		$middleware1 = $this->newMiddleware('insert', true);
		$middleware2 = $this->newMiddleware('insert', false);

		$this->repository
			->addMiddleware('CanUpdateRecord', $middleware1)
			->addMiddleware('CanAccessRecordCategory', $middleware2);

		$this->tableShouldNotReceiveInsert();

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_updates_existing_model_with_one_middleware()
	{
		$this->existingModel(['foo' => 'bar'], ['id' => 12]);

		$middleware = $this->newMiddleware('update', true);
		$this->repository->addMiddleware('CanUpadteRecord', $middleware);

		$this->tableShouldReceiveUpdate(['foo' => 'bar'], ['id' => 12]);

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_updates_existing_model_with_multiple_middlewares()
	{
		$this->existingModel(['foo' => 'bar'], ['id' => 12]);

		$middleware1 = $this->newMiddleware('update', true);
		$middleware2 = $this->newMiddleware('update', true);

		$this->repository
			->addMiddleware('CanUpdateRecord', $middleware1)
			->addMiddleware('CanAccessRecordCategory', $middleware2);

		$this->tableShouldReceiveUpdate(['foo' => 'bar'], ['id' => 12]);

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_wont_update_existing_model_with_middleware_error()
	{
		$this->existingModel(['foo' => 'bar'], ['id' => 12]);
		
		$middleware1 = $this->newMiddleware('update', true);
		$middleware2 = $this->newMiddleware('update', false);

		$this->repository
			->addMiddleware('CanUpdateRecord', $middleware1)
			->addMiddleware('CanAccessRecordCategory', $middleware2);

		$this->tableShouldNotReceiveUpdate();

		$this->repository->save($this->model);
	}

	/** @test */
	public function it_deletes_existing_model_with_one_middleware()
	{
		$this->deletableModel(['id' => 12]);

		$middleware = $this->newMiddleware('delete', true);
		$this->repository->addMiddleware('CanUpdateRecord', $middleware);

		$this->tableShouldReceiveDelete(['id' => 12]);

		$this->repository->delete($this->model);
	}

	/** @test */
	public function it_deletes_existing_model_with_multiple_middlewares()
	{
		$this->deletableModel(['id' => 12]);

		$middleware1 = $this->newMiddleware('delete', true);
		$middleware2 = $this->newMiddleware('delete', true);

		$this->repository->addMiddleware('CanUpdateRecord', $middleware1);
		$this->repository->addMiddleware('CanAccessRecordCategory', $middleware2);

		$this->tableShouldReceiveDelete(['id' => 12]);

		$this->repository->delete($this->model);
	}

	/** @test */
	public function it_wont_delete_existing_model_with_middleware_error()
	{
		$this->deletableModel(['id' => 12]);

		$middleware1 = $this->newMiddleware('delete', false);
		$middleware2 = $this->newMiddleware('delete', true);

		$this->repository->addMiddleware('CanUpdateRecord', $middleware1);
		$this->repository->addMiddleware('CanAccessRecordCategory', $middleware2);

		$this->tableShouldNotReceiveDelete();

		$this->repository->delete($this->model);
	}
}
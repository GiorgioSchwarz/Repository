<?php

namespace MolnApps\Repository;

class RepositorySelectTest extends TestCase
{
	/** @test */
	public function it_selects_all_rows()
	{
		$rows = [
			['id' => '1', 'foo' => 'bar'],
			['id' => '2', 'foo' => 'bar'],
			['id' => '3', 'foo' => 'bar'],
		];

		$this->tableShouldReceiveSelect([], $rows);
		
		$this->collectionFactoryShouldReturn($rows);

		$hydratedResult = $this->repository->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);
	}

	/** @test */
	public function it_selects_all_rows_with_query_builder_where_clause()
	{
		$rows = [
			['id' => '12', 'foo' => 'bar']
		];

		$this->tableShouldReceiveSelect(['where' => ['id' => 12]], $rows);
		
		$this->collectionFactoryShouldReturn($rows);

		$hydratedResult = $this->repository->where(['id' => 12])->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);
	}

	/** @test */
	public function it_selects_all_rows_with_query_builder_columns_clause()
	{
		$rows = [
			['id' => '12', 'foo' => 'bar']
		];

		$this->tableShouldReceiveSelect(['columns' => ['id']], $rows);
		
		$this->collectionFactoryShouldReturn($rows);

		$hydratedResult = $this->repository->columns(['id'])->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);
	}

	/** @test */
	public function it_selects_all_rows_with_query_builder_limit_clause()
	{
		$rows = [
			['id' => '1', 'foo' => 'bar'],
			['id' => '2', 'foo' => 'baz'],
			['id' => '3', 'foo' => 'bax'],
		];

		$this->tableShouldReceiveSelect(['limit' => 3], $rows);
		
		$this->collectionFactoryShouldReturn($rows);

		$hydratedResult = $this->repository->limit(3)->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);
	}

	/** @test */
	public function it_selects_all_rows_with_query_builder_offset_clause()
	{
		$rows = [
			['id' => '4', 'foo' => 'bar'],
			['id' => '5', 'foo' => 'baz'],
			['id' => '6', 'foo' => 'bax'],
		];

		$this->tableShouldReceiveSelect(['limit' => 3, 'offset' => 3], $rows);
		
		$this->collectionFactoryShouldReturn($rows);

		$hydratedResult = $this->repository->limit(3)->offset(3)->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);
	}

	/** @test */
	public function it_selects_all_rows_with_query_builder_page_alias()
	{
		$rows = [
			['id' => '4', 'foo' => 'bar'],
			['id' => '5', 'foo' => 'baz'],
			['id' => '6', 'foo' => 'bax'],
		];

		$this->tableShouldReceiveSelect(['limit' => 3, 'offset' => 3], $rows);
		
		$this->collectionFactoryShouldReturn($rows);

		$hydratedResult = $this->repository->limit(3)->page(2)->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);
	}

	/** @test */
	public function it_selects_all_rows_with_query_builder_order_clause()
	{
		$rows = [
			['id' => '4', 'foo' => 'bar'],
			['id' => '5', 'foo' => 'baz'],
			['id' => '6', 'foo' => 'bax'],
		];

		$this->tableShouldReceiveSelect(['order' => ['foo']], $rows);
		
		$this->collectionFactoryShouldReturn($rows);

		$hydratedResult = $this->repository->orderBy(['foo'])->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);
	}

	/** @test */
	public function it_resets_query_builder_after_the_query_was_run()
	{
		$rows = [];

		$this->collectionFactoryShouldReturn($rows);

		$this->table
			->method('select')
			->withConsecutive(
				[['order' => ['foo']]], 
				[['where' => ['id' => 12]]]
			)
			->willReturn($rows);

		$this->repository->orderBy(['foo'])->find();
		$this->repository->where(['id' => 12])->find();
	}
}
<?php

namespace MolnApps\Repository;

class RepositorySelectTest extends TestCase
{
	protected function setUp()
	{
		parent::setUp();
		
		$rows = [];

		$this->tableShouldReceiveExecuteSelect($rows);
		
		$this->collectionFactoryShouldReturn($rows);
	}

	/** @test */
	public function it_selects_all_rows()
	{
		$hydratedResult = $this->repository->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);

		$this->assertQueryBuilder([]);
	}

	/** @test */
	public function it_selects_all_rows_with_query_builder_where_clause()
	{
		$hydratedResult = $this->repository->where(['id' => 12])->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);

		$this->assertQueryBuilder(['where' => ['id' => 12]]);
	}

	/** @test */
	public function it_selects_all_rows_with_query_builder_columns_clause()
	{
		$hydratedResult = $this->repository->columns(['id'])->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);

		$this->assertQueryBuilder(['columns' => ['id']]);
	}

	/** @test */
	public function it_selects_all_rows_with_query_builder_limit_clause()
	{
		$hydratedResult = $this->repository->limit(3)->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);

		$this->assertQueryBuilder(['limit' => 3]);
	}

	/** @test */
	public function it_selects_all_rows_with_query_builder_offset_clause()
	{
		$hydratedResult = $this->repository->limit(3)->offset(3)->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);

		$this->assertQueryBuilder(['limit' => 3, 'offset' => 3]);
	}

	/** @test */
	public function it_selects_all_rows_with_query_builder_page_alias()
	{
		$hydratedResult = $this->repository->limit(3)->page(2)->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);

		$this->assertQueryBuilder(['limit' => 3, 'offset' => 3]);
	}

	/** @test */
	public function it_selects_all_rows_with_query_builder_order_clause()
	{
		$hydratedResult = $this->repository->orderBy(['foo'])->find();

		$this->assertInstanceOf(\StdClass::class, $hydratedResult);

		$this->assertQueryBuilder(['order' => ['foo']]);
	}

	/** @test */
	public function it_resets_query_builder_after_the_query_was_run()
	{
		$this->repository->orderBy(['foo'])->find();
		
		$this->assertQueryBuilderWasReset();
	}
}
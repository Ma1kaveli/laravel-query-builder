<?php

namespace Tests\Unit\Filters;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\PaginateFinalizer;
use Tests\TestCase;

class PaginateFinalizerTest extends TestCase
{
    /**
     * валидные параметры → paginate вызывается с ними
     */
    public function test_apply_calls_paginate_with_valid_options(): void
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('paginate')
            ->once()
            ->with(10, ['id'], 'page')
            ->andReturn($paginator);

        $finalizer = new PaginateFinalizer();

        $response = $finalizer->apply($builder, [
            'rows_per_page' => 10,
            'columns' => ['id'],
            'can_all_rows' => true,
            'page_name' => 'page',
            'max_rows_per_page' => 100,
        ]);

        $this->assertSame($paginator, $response);
    }

    /**
     * rows_per_page = -1 и can_all_rows = false → fallback на 25
     */
    public function test_apply_fallbacks_when_all_rows_not_allowed(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('paginate')
            ->once()
            ->with(25, ['*'], 'page')
            ->andReturn(Mockery::mock(LengthAwarePaginator::class));

        $finalizer = new PaginateFinalizer();

        $finalizer->apply($builder, [
            'rows_per_page' => -1,
            'columns' => ['*'],
            'can_all_rows' => false,
            'page_name' => 'page',
            'max_rows_per_page' => 100,
        ]);

        $this->assertTrue(true);
    }

    /**
     * rows_per_page больше max_rows_per_page → fallback
     */
    public function test_apply_fallbacks_when_rows_exceed_max(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('paginate')
            ->once()
            ->with(25, ['*'], 'page')
            ->andReturn(Mockery::mock(LengthAwarePaginator::class));

        $finalizer = new PaginateFinalizer();

        $finalizer->apply($builder, [
            'rows_per_page' => 500,
            'columns' => ['*'],
            'can_all_rows' => true,
            'page_name' => 'page',
            'max_rows_per_page' => 100,
        ]);

        $this->assertTrue(true);
    }

    /**
     * rows_per_page не integer → fallback
     */
    public function test_apply_fallbacks_when_rows_is_not_integer(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('paginate')
            ->once()
            ->with(25, ['*'], 'page')
            ->andReturn(Mockery::mock(LengthAwarePaginator::class));

        $finalizer = new PaginateFinalizer();

        $finalizer->apply($builder, [
            'rows_per_page' => 'ten',
            'columns' => ['*'],
            'can_all_rows' => true,
            'page_name' => 'page',
            'max_rows_per_page' => 100,
        ]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

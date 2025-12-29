<?php

namespace Tests\Unit\Filters\System;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplySortBy;
use Tests\TestCase;

class ApplySortByTest extends TestCase
{
    /**
     * sort_by не строка → ранний return
     */
    public function test_apply_returns_early_if_sort_by_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('orderBy')->never();

        $filter = new ApplySortBy();

        $filter->apply($builder, null, null, [
            'sort_by' => 123,
            'descending' => 'false',
            'available_sorts' => ['name'],
            'default_field' => 'name',
        ]);

        $this->assertTrue(true);
    }

    /**
     * sort_by не в available_sorts и default_field = null → ранний return
     */
    public function test_apply_returns_early_if_sort_by_not_available_and_no_default()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('orderBy')->never();

        $filter = new ApplySortBy();

        $filter->apply($builder, null, null, [
            'sort_by' => 'price',
            'descending' => 'false',
            'available_sorts' => ['name'],
            'default_field' => null,
        ]);

        $this->assertTrue(true);
    }

    /**
     * sort_by валиден → сортировка ASC по умолчанию
     */
    public function test_apply_orders_by_valid_sort_field_asc()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('orderBy')
            ->once()
            ->with('name', 'asc');

        $filter = new ApplySortBy();

        $filter->apply($builder, null, null, [
            'sort_by' => 'name',
            'descending' => 'false',
            'available_sorts' => ['name', 'price'],
            'default_field' => 'price',
        ]);

        $this->assertTrue(true);
    }

    /**
     * descending = true → DESC
     */
    public function test_apply_orders_desc_when_descending_true()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('orderBy')
            ->once()
            ->with('price', 'desc');

        $filter = new ApplySortBy();

        $filter->apply($builder, null, null, [
            'sort_by' => 'price',
            'descending' => 'true',
            'available_sorts' => ['name', 'price'],
            'default_field' => 'name',
        ]);

        $this->assertTrue(true);
    }

    /**
     * sort_by невалиден → используется default_field
     */
    public function test_apply_uses_default_field_when_sort_by_not_available()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('orderBy')
            ->once()
            ->with('name', 'asc');

        $filter = new ApplySortBy();

        $filter->apply($builder, null, null, [
            'sort_by' => 'unknown',
            'descending' => 'false',
            'available_sorts' => ['name'],
            'default_field' => 'name',
        ]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

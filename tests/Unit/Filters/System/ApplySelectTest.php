<?php

namespace Tests\Unit\Filters\System;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplySelect;
use Tests\TestCase;

class ApplySelectTest extends TestCase
{
    /**
     * value не массив строк → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_string_array()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('select')->never();

        $filter = new ApplySelect();
        $filter->apply($builder, null, 'name');

        $this->assertTrue(true);
    }

    /**
     * Корректный select
     */
    public function test_apply_calls_select()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('select')->once()->with(['id', 'name']);

        $filter = new ApplySelect();
        $filter->apply($builder, null, ['id', 'name']);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

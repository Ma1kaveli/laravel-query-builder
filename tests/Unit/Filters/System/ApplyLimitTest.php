<?php

namespace Tests\Unit\Filters\System;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplyLimit;
use Tests\TestCase;

class ApplyLimitTest extends TestCase
{
    /**
     * value не integer → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_integer()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('limit')->never();

        $filter = new ApplyLimit();
        $filter->apply($builder, null, 'abc');

        $this->assertTrue(true);
    }


    /**
     * Корректный limit
     */
    public function test_apply_calls_limit()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('limit')->once()->with(10);

        $filter = new ApplyLimit();
        $filter->apply($builder, null, 10);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

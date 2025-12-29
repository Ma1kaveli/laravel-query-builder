<?php

namespace Tests\Unit\Filters\System;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplyInRandomOrder;
use Tests\TestCase;

class ApplyInRandomOrderTest extends TestCase
{
    /**
     * Всегда вызывает inRandomOrder()
     */
    public function test_apply_calls_in_random_order()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('inRandomOrder')->once();

        $filter = new ApplyInRandomOrder();
        $filter->apply($builder, null, null);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

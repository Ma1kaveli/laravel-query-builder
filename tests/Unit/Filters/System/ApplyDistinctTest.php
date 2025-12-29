<?php

namespace Tests\Unit\Filters\System;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplyDistinct;
use Tests\TestCase;

class ApplyDistinctTest extends TestCase
{
    /**
     * Всегда вызывает distinct()
     */
    public function test_apply_calls_distinct()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('distinct')->once();

        $filter = new ApplyDistinct();
        $filter->apply($builder, null, null);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

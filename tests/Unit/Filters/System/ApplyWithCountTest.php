<?php

namespace Tests\Unit\Filters\System;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplyWithCount;
use Tests\TestCase;

class ApplyWithCountTest extends TestCase
{
    /**
     * withCount вызывается при валидном value
     */
    public function test_apply_calls_with_count(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('withCount')->once()->with('comments');

        $filter = new ApplyWithCount();
        $filter->apply($builder, null, 'comments');

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

<?php

namespace Tests\Unit\Filters\System;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplyWith;
use Tests\TestCase;

class ApplyWithTest extends TestCase
{
    /**
     * value не строка и не массив → return
     */
    public function test_apply_returns_early_for_invalid_value(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('with')->never();

        $filter = new ApplyWith();
        $filter->apply($builder, null, 123);

        $this->assertTrue(true);
    }

    /**
     * with строкой
     */
    public function test_apply_calls_with_with_string(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('with')->once()->with('comments');

        $filter = new ApplyWith();
        $filter->apply($builder, null, 'comments');

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

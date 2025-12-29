<?php

namespace Tests\Unit\Filters\System;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplyWithDeleted;
use Tests\TestCase;

class ApplyWithDeletedTest extends TestCase
{
    /**
     * value не bool → return
     */
    public function test_apply_returns_early_if_value_is_not_bool(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('withTrashed')->never();

        $filter = new ApplyWithDeleted();
        $filter->apply($builder, null, 'yes');

        $this->assertTrue(true);
    }

    /**
     * value true → withTrashed
     */
    public function test_apply_calls_with_trashed_when_true(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('withTrashed')->once();

        $filter = new ApplyWithDeleted();
        $filter->apply($builder, null, true);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

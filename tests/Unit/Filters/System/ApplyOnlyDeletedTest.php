<?php

namespace Tests\Unit\Filters\System;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplyOnlyDeleted;
use Tests\TestCase;

class ApplyOnlyDeletedTest extends TestCase
{
    /**
     * value не bool → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_bool()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('onlyTrashed')->never();

        $filter = new ApplyOnlyDeleted();
        $filter->apply($builder, null, 'yes');

        $this->assertTrue(true);
    }

    /**
     * value = true → вызывается onlyTrashed
     */
    public function test_apply_calls_only_trashed_when_value_true()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('onlyTrashed')->once();

        $filter = new ApplyOnlyDeleted();
        $filter->apply($builder, null, true);

        $this->assertTrue(true);
    }

    /**
     * value = false → onlyTrashed не вызывается
     */
    public function test_apply_does_not_call_only_trashed_when_value_false()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('onlyTrashed')->never();

        $filter = new ApplyOnlyDeleted();
        $filter->apply($builder, null, false);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

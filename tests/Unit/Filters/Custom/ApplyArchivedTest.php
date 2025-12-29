<?php

namespace Tests\Unit\Filters\Custom;

use Tests\TestCase;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use QueryBuilder\Filters\Custom\ApplyArchived;
use Mockery;

class ApplyArchivedTest extends TestCase
{
    /**
     * Проверяется, что к запросу добавяется onlyTrashed
     */
    public function test_apply_onlyTrashed_when_true()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('onlyTrashed')->once();

        $filter = new ApplyArchived();
        $filter->apply($builder, null, true);

        $this->assertTrue(true);
    }

    /**
     * Проверяется, что к запросу добавяется withTrashed
     */
    public function test_apply_withTrashed_when_null()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('withTrashed')->once();

        $filter = new ApplyArchived();
        $filter->apply($builder, null, null);

        $this->assertTrue(true);
    }

    /**
     * Проверяется, что к запросу ничего не добавляется
     */
    public function test_apply_does_nothing_for_false()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('onlyTrashed')->never();
        $builder->shouldReceive('withTrashed')->never();

        $filter = new ApplyArchived();
        $filter->apply($builder, null, false);

        $this->assertTrue(true);
    }

    /**
     * Проверяется, что к запросу ничего не добавляется
     */
    public function test_apply_does_nothing_for_non_bool()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('onlyTrashed')->never();
        $builder->shouldReceive('withTrashed')->never();

        $filter = new ApplyArchived();
        $filter->apply($builder, null, 'string');

        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}

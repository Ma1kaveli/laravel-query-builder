<?php

namespace Tests\Unit\Filters\System;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplyWhereIn;
use Tests\TestCase;

class ApplyWhereInTest extends TestCase
{
    /**
     * value не массив → приводится к массиву
     */
    public function test_apply_wraps_value_into_array(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('whereIn')
            ->once()
            ->with('table.id', [5], 'and');

        $filter = Mockery::mock(ApplyWhereIn::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->andReturn('table.id');

        $filter->apply($builder, 'id', 5);

        $this->assertTrue(true);
    }

    /**
     * OR whereIn
     */
    public function test_apply_calls_where_in_with_or_logic(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('whereIn')
            ->once()
            ->with('table.id', [1, 2], 'or');

        $filter = Mockery::mock(ApplyWhereIn::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->andReturn('table.id');

        $filter->apply($builder, 'id', [1, 2], [
            'is_or_where' => true,
        ]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

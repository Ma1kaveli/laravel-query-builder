<?php

namespace Tests\Unit\Filters\System;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplyBetween;
use Tests\TestCase;

class ApplyBetweenTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('whereBetween')->never();

        $filter = new ApplyBetween();
        $filter->apply($builder, ['price'], [1, 10]);

        $this->assertTrue(true);
    }


    /**
     * value валидный диапазон → ранний return (текущее поведение)
     */
    public function test_apply_returns_early_if_value_is_not_number_range()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('whereBetween')->never();

        $filter = new ApplyBetween();
        $filter->apply($builder, 'price', 'invalid');

        $this->assertTrue(true);
    }



    /**
     * AND логика
     */
    public function test_apply_calls_where_between_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('whereBetween')
            ->once()
            ->with('table.price', [1, 10], 'and');

        $filter = Mockery::mock(ApplyBetween::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->andReturn('table.price');

        $filter->apply($builder, 'price', [1, 10]);

        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_where_between_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('whereBetween')
            ->once()
            ->with('table.price', [1, 10], 'or');

        $filter = Mockery::mock(ApplyBetween::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->andReturn('table.price');

        $filter->apply($builder, 'price', [1, 10], ['is_or_where' => true]);

        $this->assertTrue(true);
    }


    protected function tearDown(): void
    {
        Mockery::close();
    }
}

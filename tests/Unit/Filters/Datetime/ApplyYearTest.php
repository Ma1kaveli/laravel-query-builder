<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyYear;
use Tests\TestCase;

class ApplyYearTest extends TestCase
{
    /**
     * field валидный год
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereYear');

        $filter = new ApplyYear();
        $filter->apply($builder, null, 2025, []);
        $this->assertTrue(true);
    }

    /**
     * value не валидный год (но логика будет отрабатывать)
     */
    public function test_apply_returns_early_if_value_is_not_year()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereYear');

        $filter = Mockery::mock(ApplyYear::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('table.created_at');

        $builder->shouldReceive('whereYear')
            ->once()
            ->with('table.created_at', 3000, null, 'and');

        $filter->apply($builder, 'created_at', 3000, []);
        $this->assertTrue(true);
    }

    /**
     * AND логика
     */
    public function test_apply_calls_whereYear_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyYear::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('table.created_at');

        $builder->shouldReceive('whereYear')
            ->once()
            ->with('table.created_at', 2025, null, 'and');

        $filter->apply($builder, 'created_at', 2025, []);
        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_whereYear_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyYear::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'updated_at')
            ->andReturn('table.updated_at');

        $builder->shouldReceive('whereYear')
            ->once()
            ->with('table.updated_at', 2023, null, 'or');

        $filter->apply($builder, 'updated_at', 2023, ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

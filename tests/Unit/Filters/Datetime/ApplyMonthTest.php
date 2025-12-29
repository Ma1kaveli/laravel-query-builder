<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyMonth;
use Tests\TestCase;

class ApplyMonthTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereMonth');

        $filter = new ApplyMonth();
        $filter->apply($builder, null, 5, []);

        $this->assertTrue(true);
    }

    /**
     * value не месяц → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_month()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereMonth');

        $filter = new ApplyMonth();
        $filter->apply($builder, 'created_at', 13, []);

        $this->assertTrue(true);
    }

    /**
     * AND логика по умолчанию
     */
    public function test_apply_calls_whereMonth_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyMonth::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('table.created_at');

        $builder->shouldReceive('whereMonth')
            ->once()
            ->with('table.created_at', 5, null, 'and');

        $filter->apply($builder, 'created_at', 5, []);

        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_whereMonth_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyMonth::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'updated_at')
            ->andReturn('table.updated_at');

        $builder->shouldReceive('whereMonth')
            ->once()
            ->with('table.updated_at', 7, null, 'or');

        $filter->apply($builder, 'updated_at', 7, ['is_or_where' => true]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

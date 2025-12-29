<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyDateRange;
use Tests\TestCase;

class ApplyDateRangeTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereBetween');

        $filter = new ApplyDateRange();
        $filter->apply(
            $builder,
            ['created_at'],
            ['2024-01-01', '2024-01-31'],
            []
        );

        $this->assertTrue(true);
    }

    /**
     * value не диапазон дат → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_date_range()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereBetween');

        $filter = new ApplyDateRange();
        $filter->apply(
            $builder,
            'created_at',
            '2024-01-01',
            []
        );

        $this->assertTrue(true);
    }

    /**
     * AND логика по умолчанию
     */
    public function test_apply_calls_where_between_with_and_logic()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyDateRange::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('users.created_at');

        $builder->shouldReceive('whereBetween')
            ->once()
            ->with(
                'users.created_at',
                ['2024-01-01', '2024-01-31'],
                'and'
            );

        $filter->apply(
            $builder,
            'created_at',
            ['2024-01-01', '2024-01-31'],
            []
        );

        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_where_between_with_or_logic()
    {
        $this->setDateTimeFormat();
        
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyDateRange::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'ended_at')
            ->andReturn('orders.ended_at');

        $builder->shouldReceive('whereBetween')
            ->once()
            ->with(
                'orders.ended_at',
                ['2023-01-01', '2023-12-31'],
                'or'
            );

        $filter->apply(
            $builder,
            'ended_at',
            ['2023-01-01', '2023-12-31'],
            [
                'is_or_where' => true
            ]
        );

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyDateStart;
use Tests\TestCase;

class ApplyDateStartTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $this->setDateTimeFormat();
        
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyDateStart();
        $filter->apply(
            $builder,
            ['created_at'],
            '2024-01-01',
            []
        );

        $this->assertTrue(true);
    }

    /**
     * value не дата → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_date()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyDateStart();
        $filter->apply(
            $builder,
            'created_at',
            'not-a-date',
            []
        );

        $this->assertTrue(true);
    }

    /**
     * AND логика по умолчанию
     */
    public function test_apply_calls_where_with_and_logic()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyDateStart::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('users.created_at');

        $builder->shouldReceive('where')
            ->once()
            ->with(
                'users.created_at',
                '>=',
                '2024-01-01',
                'and'
            );

        $filter->apply(
            $builder,
            'created_at',
            '2024-01-01',
            []
        );

        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_where_with_or_logic()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyDateStart::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'started_at')
            ->andReturn('orders.started_at');

        $builder->shouldReceive('where')
            ->once()
            ->with(
                'orders.started_at',
                '>=',
                '2023-06-01',
                'or'
            );

        $filter->apply(
            $builder,
            'started_at',
            '2023-06-01',
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

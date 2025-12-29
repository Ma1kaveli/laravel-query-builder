<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyDateEnd;
use Tests\TestCase;

class ApplyDateEndTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyDateEnd();
        $filter->apply($builder, ['created_at'], '2024-01-31', []);

        $this->assertTrue(true);
    }

    /**
     * value не дата → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_date()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyDateEnd();
        $filter->apply($builder, 'created_at', 'invalid-date', []);

        $this->assertTrue(true);
    }

    /**
     * AND по умолчанию + оператор <=
     */
    public function test_apply_calls_where_with_less_or_equal_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyDateEnd::class)
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
                '<=',
                '2024-01-31',
                'and'
            );

        $filter->apply($builder, 'created_at', '2024-01-31', []);

        $this->assertTrue(true);
    }

    /**
     * OR логика + оператор <=
     */
    public function test_apply_calls_where_with_less_or_equal_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyDateEnd::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'ended_at')
            ->andReturn('orders.ended_at');

        $builder->shouldReceive('where')
            ->once()
            ->with(
                'orders.ended_at',
                '<=',
                '2023-12-31',
                'or'
            );

        $filter->apply($builder, 'ended_at', '2023-12-31', [
            'is_or_where' => true
        ]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyDate;
use Tests\TestCase;

class ApplyDateTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyDate();
        $filter->apply($builder, ['created_at'], '2024-01-01', []);

        $this->assertTrue(true);
    }

    /**
     * value не дата → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_date()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyDate();
        $filter->apply($builder, 'created_at', 'not-a-date', []);

        $this->assertTrue(true);
    }

    /**
     * is_or_where отсутствует → используется AND
     */
    public function test_apply_calls_where_with_and_logic_by_default()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyDate::class)
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
                '2024-01-01',
                null,
                'and'
            );

        $filter->apply($builder, 'created_at', '2024-01-01', []);

        $this->assertTrue(true);
    }

    /**
     * is_or_where = true → OR
     */
    public function test_apply_calls_where_with_or_logic()
    {
        $this->setDateTimeFormat();
        
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyDate::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'updated_at')
            ->andReturn('orders.updated_at');

        $builder->shouldReceive('where')
            ->once()
            ->with(
                'orders.updated_at',
                '2023-12-31',
                null,
                'or'
            );

        $filter->apply($builder, 'updated_at', '2023-12-31', [
            'is_or_where' => true
        ]);

        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}

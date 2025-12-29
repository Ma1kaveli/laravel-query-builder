<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyCurrentHour;
use Tests\TestCase;

class ApplyCurrentHourTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldNotReceive('whereRaw');

        $filter = new ApplyCurrentHour();
        $filter->apply($builder, ['time'], null, ['is_or_where' => false]);

        $this->assertTrue(true);
    }

    /**
     * is_or_where = false → and
     */
    public function test_apply_calls_whereRaw_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyCurrentHour::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('table.created_at');

        $builder->shouldReceive('whereRaw')
            ->once()
            ->with(
                'HOUR(table.created_at) = HOUR(CURRENT_TIME())',
                [],
                'and'
            );

        $filter->apply($builder, 'created_at', null, [
            'is_or_where' => false
        ]);

        $this->assertTrue(true);
    }

    /**
     * is_or_where = true → or
     */
    public function test_apply_calls_whereRaw_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyCurrentHour::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'updated_at')
            ->andReturn('orders.updated_at');

        $builder->shouldReceive('whereRaw')
            ->once()
            ->with(
                'HOUR(orders.updated_at) = HOUR(CURRENT_TIME())',
                [],
                'or'
            );

        $filter->apply($builder, 'updated_at', null, [
            'is_or_where' => true
        ]);

        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}

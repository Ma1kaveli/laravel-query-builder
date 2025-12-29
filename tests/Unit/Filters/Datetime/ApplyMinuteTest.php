<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Factories\Datetime\DateTimeExpressionFactory;
use QueryBuilder\Filters\Datetime\ApplyMinute;
use Tests\TestCase;

class ApplyMinuteTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereRaw');

        $filter = new ApplyMinute();
        $filter->apply($builder, null, 10, []);

        $this->assertTrue(true);
    }

    /**
     * value не минута → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_minute()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereRaw');

        $filter = new ApplyMinute();
        $filter->apply($builder, 'created_at', 100, []);

        $this->assertTrue(true);
    }

    /**
     * AND логика по умолчанию
     */
    public function test_apply_calls_whereRaw_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyMinute::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('table.created_at');

        // подменяем фабрику выражений
        $filter->shouldReceive('getFieldWithTable')
            ->andReturn('table.created_at');

        $builder->shouldReceive('whereRaw')
            ->once()
            ->with('MINUTE(table.created_at) = ?', [15], 'and');

        // Мокаем фабрику
        $factoryMock = Mockery::mock('overload:' . DateTimeExpressionFactory::class);
        $factoryMock->shouldReceive('make->minute')
            ->once()
            ->with('table.created_at')
            ->andReturn('MINUTE(table.created_at)');

        $filter->apply($builder, 'created_at', 15, []);

        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_whereRaw_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyMinute::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'updated_at')
            ->andReturn('table.updated_at');

        $builder->shouldReceive('whereRaw')
            ->once()
            ->with('MINUTE(table.updated_at) = ?', [30], 'or');

        // Мокаем фабрику
        $factoryMock = Mockery::mock('overload:' . DateTimeExpressionFactory::class);
        $factoryMock->shouldReceive('make->minute')
            ->once()
            ->with('table.updated_at')
            ->andReturn('MINUTE(table.updated_at)');

        $filter->apply($builder, 'updated_at', 30, ['is_or_where' => true]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

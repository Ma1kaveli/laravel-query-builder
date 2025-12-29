<?php

namespace Tests\Unit\Filters\Datetime;

use Mockery;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use QueryBuilder\Filters\Datetime\ApplyHour;
use QueryBuilder\Factories\Datetime\DateTimeExpressionFactory;

class ApplyHourTest extends TestCase
{
    /**
     * Проверка применения обычного where
     */
    public function test_apply_calls_whereRaw()
    {
        $modelMock = Mockery::mock();
        $modelMock->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($modelMock);

        $builder->shouldReceive('whereRaw')
            ->once()
            ->with('HOUR(table.field) = ?', [15], 'and');


        // Мокаем фабрику
        $factoryMock = Mockery::mock('overload:' . DateTimeExpressionFactory::class);
        $factoryMock->shouldReceive('make->hour')
            ->once()
            ->with('table.field')
            ->andReturn('HOUR(table.field)');

        $filter = new ApplyHour();

        $filter->apply($builder, 'field', 15, ['is_or_where' => false]);
        $this->assertTrue(true);
    }

    /**
     * Проверка применения orWhere
     */
    public function test_apply_calls_orWhereRaw()
    {
        $modelMock = Mockery::mock();
        $modelMock->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($modelMock);

        $builder->shouldReceive('whereRaw')
            ->once()
            ->with('HOUR(table.field) = ?', [23], 'or');


        // Мокаем фабрику
        $factoryMock = Mockery::mock('overload:' . DateTimeExpressionFactory::class);
        $factoryMock->shouldReceive('make->hour')
            ->once()
            ->with('table.field')
            ->andReturn('HOUR(table.field)');

        $filter = new ApplyHour();

        $filter->apply($builder, 'field', 23, ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    /**
     * Проверка игнорирования некорректного поля
     */
    public function test_apply_ignores_non_string_field()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereRaw');

        $filter = new ApplyHour();
        $filter->apply($builder, ['not', 'string'], 10, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка игнорирования некорректного значения
     */
    public function test_apply_ignores_invalid_hour_value()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereRaw');

        $filter = new ApplyHour();
        $filter->apply($builder, 'field', 99, []);
        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}

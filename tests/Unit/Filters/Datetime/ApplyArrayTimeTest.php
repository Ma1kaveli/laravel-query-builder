<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyArrayTime;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Tests\TestCase;

class ApplyArrayTimeTest extends TestCase
{
    /**
     * $field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldNotReceive('where');

        $filter = new ApplyArrayTime();
        $filter->apply($builder, ['time'], ['12:00'], ['is_or_where' => false]);

        $this->assertTrue(true);
    }

    /**
     * $value не массив времени → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_time_array()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldNotReceive('where');

        $filter = new ApplyArrayTime();
        $filter->apply($builder, 'start_time', ['invalid'], ['is_or_where' => false]);

        $this->assertTrue(true);
    }

    /**
     * Проверка обычного массива времени с is_or_where = false
     */
    public function test_apply_calls_whereTime_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        // Замокаем getFieldWithTable
        $filter = Mockery::mock(ApplyArrayTime::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'start_time')
            ->andReturn('table.start_time');

        $builder->shouldReceive('where')
            ->once()
            ->with(Mockery::on(function ($closure) use ($builder) {
                // Внутри замыкаем вызовы whereTime и orWhere
                $builder->shouldReceive('whereTime')->once()->with('table.start_time', '12:00');
                $closure($builder);
                return true;
            }), null, null, 'and');

        $filter->apply($builder, 'start_time', ['12:00'], ['is_or_where' => false]);
        $this->assertTrue(true);
    }

    /**
     * Проверка массива времени с is_or_where = true
     */
    public function test_apply_calls_whereTime_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyArrayTime::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'end_time')
            ->andReturn('table.end_time');

        $builder->shouldReceive('where')
            ->once()
            ->with(Mockery::on(function ($closure) use ($builder) {
                $builder->shouldReceive('whereTime')->once()->with('table.end_time', '14:00');
                $closure($builder);
                return true;
            }), null, null, 'or');

        $filter->apply($builder, 'end_time', ['14:00'], ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    /**
     * Проверка нескольких элементов в массиве → orWhere для второго и последующих
     */
    public function test_apply_multiple_times_creates_orWhere_for_others()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyArrayTime::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'time_field')
            ->andReturn('table.time_field');

        $builder->shouldReceive('where')
            ->once()
            ->with(Mockery::on(function ($closure) use ($builder) {
                $builder->shouldReceive('whereTime')->once()->with('table.time_field', '08:00');
                $builder->shouldReceive('orWhere')->once()->with(Mockery::on(function ($subClosure) use ($builder) {
                    $builder->shouldReceive('whereTime')->once()->with('table.time_field', '09:00');
                    $subClosure($builder);
                    return true;
                }));
                $closure($builder);
                return true;
            }), null, null, 'and');

        $filter->apply($builder, 'time_field', ['08:00', '09:00'], ['is_or_where' => false]);
        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}

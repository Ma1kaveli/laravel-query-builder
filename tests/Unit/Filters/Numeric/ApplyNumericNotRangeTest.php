<?php

namespace Tests\Unit\Filters\Numeric;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Numeric\ApplyNumericNotRange;
use Tests\TestCase;

class ApplyNumericNotRangeTest extends TestCase
{
    /**
     * Поле не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereNotBetween');

        $filter = new ApplyNumericNotRange();
        $filter->apply($builder, null, [1, 10], []);
        $this->assertTrue(true);
    }

    /**
     * Значение не диапазон → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_range()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereNotBetween');

        $filter = new ApplyNumericNotRange();
        $filter->apply($builder, 'field', [1], []);
        $this->assertTrue(true);
    }

    /**
     * Проверка whereNotBetween с AND логикой
     */
    public function test_apply_calls_whereNotBetween_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyNumericNotRange::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('whereNotBetween')->once()->with('table.field', [1, 10], 'and');

        $filter->apply($builder, 'field', [1, 10], []);
        $this->assertTrue(true);
    }

    /**
     * Проверка OR логики
     */
    public function test_apply_calls_whereNotBetween_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyNumericNotRange::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('whereNotBetween')->once()->with('table.field', [5, 15], 'or');

        $filter->apply($builder, 'field', [5, 15], ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

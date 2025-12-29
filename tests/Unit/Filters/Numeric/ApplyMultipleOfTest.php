<?php

namespace Tests\Unit\Filters\Numeric;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Numeric\ApplyMultipleOf;
use Tests\TestCase;

class ApplyMultipleOfTest extends TestCase
{
    /**
     * Поле не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereRaw');

        $filter = new ApplyMultipleOf();
        $filter->apply($builder, null, 2, []);
        $this->assertTrue(true);
    }

    /**
     * Значение не numeric или делитель 0 → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_numeric_or_zero()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereRaw');

        $filter = new ApplyMultipleOf();
        $filter->apply($builder, 'field', 0, []);
        $filter->apply($builder, 'field', 'a', []);
        $this->assertTrue(true);
    }

    /**
     * Проверка whereRaw с AND логикой
     */
    public function test_apply_calls_whereRaw_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyMultipleOf::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'field')
            ->andReturn('table.field');
            
        $builder->shouldReceive('whereRaw')->once()->with('MOD(table.field, ?) = 0', [3], 'and');

        $filter->apply($builder, 'field', 3, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка OR логики
     */
    public function test_apply_calls_whereRaw_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyMultipleOf::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'field')
            ->andReturn('table.field');

        $builder->shouldReceive('whereRaw')->once()->with('MOD(table.field, ?) = 0', [5], 'or');

        $filter->apply($builder, 'field', 5, ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

<?php

namespace Tests\Unit\Filters\Numeric;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Numeric\ApplyNumericGreaterThan;
use Tests\TestCase;

class ApplyNumericGreaterThanTest extends TestCase
{
    /**
     * Поле не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyNumericGreaterThan();
        $filter->apply($builder, null, 5, []);
        $this->assertTrue(true);
    }

    /**
     * Значение не numeric → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_numeric()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyNumericGreaterThan();
        $filter->apply($builder, 'field', 'a', []);
        $this->assertTrue(true);
    }

    /**
     * Проверка where с AND логикой
     */
    public function test_apply_calls_where_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyNumericGreaterThan::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('where')->once()->with('table.field', '>', 10, 'and');

        $filter->apply($builder, 'field', 10, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка OR логики
     */
    public function test_apply_calls_where_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyNumericGreaterThan::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('where')->once()->with('table.field', '>', 10, 'or');

        $filter->apply($builder, 'field', 10, ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

<?php

namespace Tests\Unit\Filters\Numeric;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Numeric\ApplyArrayDouble;
use Tests\TestCase;

class ApplyArrayDoubleTest extends TestCase
{
    /**
     * Если поле не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereIn');

        $filter = new ApplyArrayDouble();
        $filter->apply($builder, null, [1.1, 2.2], []);
        $this->assertTrue(true);
    }

    /**
     * Если value не массив double → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_double_array()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereIn');

        $filter = new ApplyArrayDouble();
        $filter->apply($builder, 'field', ['a', 'b'], []);
        $this->assertTrue(true);
    }

    /**
     * Если value пустой массив → ранний return
     */
    public function test_apply_returns_early_if_value_is_empty_array()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereIn');

        $filter = new ApplyArrayDouble();
        $filter->apply($builder, 'field', [], []);
        $this->assertTrue(true);
    }

    /**
     * Проверка whereIn с AND логикой
     */
    public function test_apply_calls_whereIn_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyArrayDouble::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('whereIn')->once()->with('table.field', [1.1, 2.2], 'and');

        $filter->apply($builder, 'field', [1.1, 2.2], []);
        $this->assertTrue(true);
    }

    /**
     * Проверка whereIn с OR логикой
     */
    public function test_apply_calls_whereIn_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyArrayDouble::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('whereIn')->once()->with('table.field', [1.1, 2.2], 'or');

        $filter->apply($builder, 'field', [1.1, 2.2], ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

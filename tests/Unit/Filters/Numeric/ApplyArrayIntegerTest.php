<?php

namespace Tests\Unit\Filters\Numeric;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Numeric\ApplyArrayInteger;
use Tests\TestCase;

class ApplyArrayIntegerTest extends TestCase
{
    /**
     * Поле не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereIn');

        $filter = new ApplyArrayInteger();
        $filter->apply($builder, null, [1, 2], []);
        $this->assertTrue(true);
    }

    /**
     * value не integer → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_integer_array()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereIn');

        $filter = new ApplyArrayInteger();
        $filter->apply($builder, 'field', ['a', 'b'], []);
        $this->assertTrue(true);
    }

    /**
     * whereIn с AND логикой
     */
    public function test_apply_calls_whereIn_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyArrayInteger::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('whereIn')->once()->with('table.field', [1, 2], 'and');

        $filter->apply($builder, 'field', [1, 2], []);
        $this->assertTrue(true);
    }

    /**
     * whereIn с OR логикой
     */
    public function test_apply_calls_whereIn_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyArrayInteger::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('whereIn')->once()->with('table.field', [1, 2], 'or');

        $filter->apply($builder, 'field', [1, 2], ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

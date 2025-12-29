<?php

namespace Tests\Unit\Filters\Logic;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Logic\ApplyNotNull;
use Tests\TestCase;

class ApplyNotNullTest extends TestCase
{
    /**
     * Если поле не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereNotNull');

        $filter = new ApplyNotNull();
        $filter->apply($builder, null, null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка вызова whereNotNull с AND логикой
     */
    public function test_apply_calls_whereNotNull_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyNotNull::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('whereNotNull')->once()->with('table.field', 'and');

        $filter->apply($builder, 'field', null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка OR логики
     */
    public function test_apply_calls_whereNotNull_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyNotNull::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('whereNotNull')->once()->with('table.field', 'or');

        $filter->apply($builder, 'field', null, ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

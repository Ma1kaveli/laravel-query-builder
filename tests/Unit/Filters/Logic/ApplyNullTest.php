<?php

namespace Tests\Unit\Filters\Logic;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Logic\ApplyNull;
use Tests\TestCase;

class ApplyNullTest extends TestCase
{
    /**
     * Если поле не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereNull');

        $filter = new ApplyNull();
        $filter->apply($builder, null, null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка вызова whereNull с AND логикой
     */
    public function test_apply_calls_whereNull_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyNull::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('whereNull')->once()->with('table.field', 'and');

        $filter->apply($builder, 'field', null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка OR логики
     */
    public function test_apply_calls_whereNull_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyNull::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('whereNull')->once()->with('table.field', 'or');

        $filter->apply($builder, 'field', null, ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

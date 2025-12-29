<?php

namespace Tests\Unit\Filters\Logic;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Logic\ApplyFalse;
use Tests\TestCase;

class ApplyFalseTest extends TestCase
{
    /**
     * Если поле не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyFalse();
        $filter->apply($builder, null, null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка вызова where с false и AND логикой
     */
    public function test_apply_calls_where_with_false_and_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyFalse::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('where')->once()->with('table.field', false, null, 'and');

        $filter->apply($builder, 'field', null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка OR логики
     */
    public function test_apply_calls_where_with_false_and_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyFalse::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('where')->once()->with('table.field', false, null, 'or');

        $filter->apply($builder, 'field', null, ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

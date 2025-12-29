<?php

namespace Tests\Unit\Filters\String;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\String\ApplyString;
use Tests\TestCase;

class ApplyStringTest extends TestCase
{
    /**
     * field или value не строка → ранний return
     */
    public function test_apply_returns_early_if_field_or_value_is_invalid()
    {
        $model = Mockery::mock();
        $model->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($model);
        $builder->shouldReceive('where')->never();

        $filter = new ApplyString();

        $filter->apply($builder, null, 'value');

        $this->assertTrue(true);
    }

    /**
     * AND логика
     */
    public function test_apply_and_logic()
    {
        $model = Mockery::mock();
        $model->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($model);
        $builder->shouldReceive('where')
            ->once()
            ->with('table.name', 'value', null, 'and');

        $filter = Mockery::mock(ApplyString::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $filter->shouldReceive('getFieldWithTable')->andReturn('table.name');

        $filter->apply($builder, 'name', 'value');
        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_or_logic()
    {
        $model = Mockery::mock();
        $model->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($model);
        $builder->shouldReceive('where')
            ->once()
            ->with('table.name', 'value', null, 'or');

        $filter = Mockery::mock(ApplyString::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $filter->shouldReceive('getFieldWithTable')->andReturn('table.name');

        $filter->apply($builder, 'name', 'value', ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

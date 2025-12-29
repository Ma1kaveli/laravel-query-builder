<?php

namespace Tests\Unit\Filters\String;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\String\ApplyRegex;
use Tests\TestCase;

class ApplyRegexTest extends TestCase
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

        $filter = new ApplyRegex();

        $filter->apply($builder, null, 'regex');

        $this->assertTrue(true);
    }


    /**
     * value невалидный для REGEXP → ранний return
     */
    public function test_apply_returns_early_if_value_invalid_regex()
    {
        $model = Mockery::mock();
        $model->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($model);
        $builder->shouldNotReceive('where');

        $filter = new ApplyRegex();

        $filter->apply($builder, 'name', 'invalid?value'); // "?" не разрешен
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
            ->with('table.name', 'REGEXP', 'value', 'and');

        $filter = Mockery::mock(ApplyRegex::class)
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
            ->with('table.name', 'REGEXP', 'value', 'or');

        $filter = Mockery::mock(ApplyRegex::class)
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

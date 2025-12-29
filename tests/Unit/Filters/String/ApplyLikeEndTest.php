<?php

namespace Tests\Unit\Filters\String;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\String\ApplyLikeEnd;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ApplyLikeEndTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyLikeEnd();
        $filter->apply($builder, null, 'value', []);

        $this->assertTrue(true);
    }

    /**
     * value не строка или пустая → ранний return
     */
    public function test_apply_returns_early_if_value_is_invalid()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyLikeEnd();
        $filter->apply($builder, 'name', '', []);
        $filter->apply($builder, 'name', 123, []);

        $this->assertTrue(true);
    }

    /**
     * AND логика по умолчанию
     */
    public function test_apply_calls_where_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('where')->once();

        $filter = Mockery::mock(ApplyLikeEnd::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->andReturn('table.name');

        $filter->apply($builder, 'name', 'value');

        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_where_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('where')->once();

        $filter = Mockery::mock(ApplyLikeEnd::class)
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

<?php

namespace Tests\Unit\Filters\Special;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Special\ApplyRating;
use Tests\TestCase;

class ApplyRatingTest extends TestCase
{
    /**
     * Ранний return если поле не строка или значение не числовое
     */
    public function test_apply_returns_early_if_field_or_value_invalid()
    {
        $modelMock = Mockery::mock();
        $modelMock->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($modelMock);
        $builder->shouldNotReceive('where');

        $filter = new ApplyRating();
        $filter->apply($builder, null, 'abc', []);
        $this->assertTrue(true);
    }

    /**
     * Проверка where с AND
     */
    public function test_apply_calls_where_and()
    {
        $modelMock = Mockery::mock();
        $modelMock->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($modelMock);
        $builder->shouldReceive('where')->once()->with('table.field', '>=', 5, 'and');

        $filter = new ApplyRating();
        $filter->apply($builder, 'field', 5, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка where с OR
     */
    public function test_apply_calls_where_or()
    {
        $modelMock = Mockery::mock();
        $modelMock->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($modelMock);
        $builder->shouldReceive('where')->once()->with('table.field', '>=', 5, 'or');

        $filter = new ApplyRating();
        $filter->apply($builder, 'field', 5, ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

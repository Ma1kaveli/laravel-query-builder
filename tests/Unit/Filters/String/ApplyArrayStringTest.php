<?php

namespace Tests\Unit\Filters\String;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\String\ApplyArrayString;
use Tests\TestCase;

class ApplyArrayStringTest extends TestCase
{
    /**
     * Проверка: если поле не строка, фильтр возвращает early
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereIn');

        $filter = new ApplyArrayString();
        $filter->apply($builder, null, ['a', 'b'], []);

        $this->assertTrue(true);
    }

    /**
     * Проверка: если value не массив строк, фильтр возвращает early
     */
    public function test_apply_returns_early_if_value_is_not_string_array()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereIn');

        $filter = new ApplyArrayString();
        $filter->apply($builder, 'name', 'string', []);

        $this->assertTrue(true);
    }

    /**
     * Проверка whereIn с логикой AND (по умолчанию)
     */
    public function test_apply_calls_whereIn_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturnSelf();

        $filter = Mockery::mock(ApplyArrayString::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'name')
            ->andReturn('table.name');

        $builder->shouldReceive('whereIn')
            ->once()
            ->with('table.name', ['a', 'b'], 'and');

        $filter->apply($builder, 'name', ['a', 'b'], []);

        $this->assertTrue(true);
    }

    /**
     * Проверка whereIn с логикой OR
     */
    public function test_apply_calls_whereIn_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturnSelf();

        $filter = Mockery::mock(ApplyArrayString::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'tags')
            ->andReturn('table.tags');

        $builder->shouldReceive('whereIn')
            ->once()
            ->with('table.tags', ['x', 'y'], 'or');

        $filter->apply($builder, 'tags', ['x', 'y'], ['is_or_where' => true]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

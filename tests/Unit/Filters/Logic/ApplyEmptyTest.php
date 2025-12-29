<?php

namespace Tests\Unit\Filters\Logic;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Logic\ApplyEmpty;
use Tests\TestCase;

class ApplyEmptyTest extends TestCase
{
    /**
     * Если поле не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyEmpty();
        $filter->apply($builder, null, null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка корректного вызова вложенного where с AND логикой
     */
    public function test_apply_calls_where_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyEmpty::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'field')
            ->andReturn('table.field');

        $builder->shouldReceive('where')
            ->once()
            ->withArgs(function ($closure, $a, $b, $logic) {
                $mockQuery = Mockery::mock(EloquentBuilder::class);
                $mockQuery->shouldReceive('whereNull')->once()->with('table.field')->andReturnSelf();
                $mockQuery->shouldReceive('orWhere')->once()->with('table.field', '')->andReturnSelf();
                $mockQuery->shouldReceive('orWhereJsonLength')->once()->with('table.field', 0)->andReturnSelf();

                $closure($mockQuery);
                return $logic === 'and';
            });

        $filter->apply($builder, 'field', null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка OR логики
     */
    public function test_apply_calls_where_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyEmpty::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'field')
            ->andReturn('table.field');

        $builder->shouldReceive('where')->once()->withArgs(fn($closure, $a, $b, $logic) => $logic === 'or');

        $filter->apply($builder, 'field', null, ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

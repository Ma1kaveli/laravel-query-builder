<?php

namespace Tests\Unit\Filters\Logic;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Logic\ApplyBoolean;
use Tests\TestCase;

class ApplyBooleanTest extends TestCase
{
    /**
     * Если поле не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyBoolean();
        $filter->apply($builder, null, true, []);
        $this->assertTrue(true);
    }

    /**
     * Если значение не булево → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_bool()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyBoolean();
        $filter->apply($builder, 'active', 'yes', []);
        $this->assertTrue(true);
    }

    /**
     * Проверка вызова where с AND логикой
     */
    public function test_apply_calls_where_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyBoolean::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'active')
            ->andReturn('table.active');

        $builder->shouldReceive('where')
            ->once()
            ->with('table.active', true, null, 'and');

        $filter->apply($builder, 'active', true, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка вызова where с OR логикой
     */
    public function test_apply_calls_where_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyBoolean::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'deleted')
            ->andReturn('table.deleted');

        $builder->shouldReceive('where')
            ->once()
            ->with('table.deleted', false, null, 'or');

        $filter->apply($builder, 'deleted', false, ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

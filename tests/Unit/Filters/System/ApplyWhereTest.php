<?php

namespace Tests\Unit\Filters\System;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplyWhere;
use Tests\TestCase;

class ApplyWhereTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('where')->never();

        $filter = new ApplyWhere();
        $filter->apply($builder, ['field'], 'value');

        $this->assertTrue(true);
    }

    /**
     * where с AND логикой по умолчанию
     */
    public function test_apply_calls_where_with_and_logic(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('where')
            ->once()
            ->with('table.name', 'value', null, 'and');

        $filter = Mockery::mock(ApplyWhere::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->andReturn('table.name');

        $filter->apply($builder, 'name', 'value');

        $this->assertTrue(true);
    }

    /**
     * where с OR логикой
     */
    public function test_apply_calls_where_with_or_logic(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('where')
            ->once()
            ->with('table.name', 'value', null, 'or');

        $filter = Mockery::mock(ApplyWhere::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->andReturn('table.name');

        $filter->apply($builder, 'name', 'value', [
            'is_or_where' => true,
        ]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

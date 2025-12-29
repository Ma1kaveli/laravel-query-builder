<?php

namespace Tests\Unit\Filters\System;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\System\ApplyWhereHas;
use Tests\TestCase;

class ApplyWhereHasTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('when')->never();

        $filter = new ApplyWhereHas();
        $filter->apply($builder, ['rel'], null, []);

        $this->assertTrue(true);
    }

    /**
     * whereHas с AND логикой
     */
    public function test_apply_calls_where_has_with_and_logic(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('when')
            ->once()
            ->with(
                false,
                Mockery::type(Closure::class),
                Mockery::type(Closure::class)
            );

        $filter = new ApplyWhereHas();

        $filter->apply(
            $builder,
            'comments',
            null,
            [
                'is_or_where' => false,
                'sub_query' => fn ($q) => $q,
            ]
        );

        $this->assertTrue(true);
    }

    /**
     * orWhereHas логика
     */
    public function test_apply_calls_where_has_with_or_logic(): void
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('when')
            ->once()
            ->with(
                true,
                Mockery::type(Closure::class),
                Mockery::type(Closure::class)
            );

        $filter = new ApplyWhereHas();

        $filter->apply(
            $builder,
            'comments',
            null,
            [
                'is_or_where' => true,
                'sub_query' => fn ($q) => $q,
            ]
        );

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

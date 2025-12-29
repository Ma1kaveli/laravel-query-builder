<?php

namespace Tests\Unit\Filters\Combine;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Expression;
use Mockery;
use QueryBuilder\Filters\Combine\ApplyWhereHasLikeArray;
use Tests\TestCase;

class ApplyWhereHasLikeArrayTest extends TestCase
{
    /**
     * Проверка, когда параметр is_deep_or_where = false и одно поле
     */
    public function test_apply_calls_whereHas_with_single_field_and_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $relatedBuilder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('when')
            ->once()
            ->with(false, Mockery::type('Closure'), Mockery::type('Closure'))
            ->andReturnUsing(function ($condition, $ifTrue, $ifFalse) use ($relatedBuilder) {
                if ($condition) {
                    $ifTrue($relatedBuilder);
                } else {
                    $ifFalse($relatedBuilder);
                }
                return $relatedBuilder;
            });

        $relatedBuilder->shouldReceive('whereHas')
            ->once()
            ->with('relation', Mockery::on(function ($closure) use ($relatedBuilder) {
                $closure($relatedBuilder);
                return true;
            }));

        $relatedBuilder->shouldReceive('where')
            ->once()
            ->with(
                Mockery::type(Expression::class),
                'LIKE',
                '%value%',
                'and'
            );

        $filter = new ApplyWhereHasLikeArray();

        $filter->apply($builder, 'field1', 'value', [
            'relationship' => 'relation',
            'is_or_where' => false,
            'is_deep_or_where' => false
        ]);

        $this->assertTrue(true);
    }

    /**
     * Проверка, когда параметр is_deep_or_where = true и несколько полей
     */
    public function test_apply_calls_orWhereHas_with_multiple_fields_and_deep_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $relatedBuilder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('when')
            ->once()
            ->with(true, Mockery::type('Closure'), Mockery::type('Closure'))
            ->andReturnUsing(function ($condition, $ifTrue, $ifFalse) use ($relatedBuilder) {
                if ($condition) {
                    $ifTrue($relatedBuilder);
                } else {
                    $ifFalse($relatedBuilder);
                }
                return $relatedBuilder;
            });

        $relatedBuilder->shouldReceive('orWhereHas')
            ->once()
            ->with('relation', Mockery::on(function ($closure) use ($relatedBuilder) {
                $closure($relatedBuilder);
                return true;
            }));

        $relatedBuilder->shouldReceive('where')
            ->once()
            ->with(
                Mockery::type(Expression::class),
                'LIKE',
                '%value%',
                'and'
            );

        $relatedBuilder->shouldReceive('where')
            ->once()
            ->with(
                Mockery::type(Expression::class),
                'LIKE',
                '%value%',
                'or'
            );

        $filter = new ApplyWhereHasLikeArray();

        $filter->apply($builder, ['field1', 'field2'], 'value', [
            'relationship' => 'relation',
            'is_or_where' => true,
            'is_deep_or_where' => true
        ]);

        $this->assertTrue(true);
    }

    /**
     * Проверка, когда параметр is_deep_or_where = false и несколько полей
     */
    public function test_apply_calls_whereHas_with_multiple_fields_and_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $relatedBuilder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('when')
            ->once()
            ->with(false, Mockery::type('Closure'), Mockery::type('Closure'))
            ->andReturnUsing(function ($condition, $ifTrue, $ifFalse) use ($relatedBuilder) {
                if ($condition) {
                    $ifTrue($relatedBuilder);
                } else {
                    $ifFalse($relatedBuilder);
                }
                return $relatedBuilder;
            });

        $relatedBuilder->shouldReceive('whereHas')
            ->once()
            ->with('relation', Mockery::on(function ($closure) use ($relatedBuilder) {
                $closure($relatedBuilder);
                return true;
            }));

        $relatedBuilder->shouldReceive('where')
            ->once()
            ->with(
                Mockery::type(Expression::class),
                'LIKE',
                '%value%',
                'and'
            );

        $relatedBuilder->shouldReceive('where')
            ->once()
            ->with(
                Mockery::type(Expression::class),
                'LIKE',
                '%value%',
                'and'
            );

        $filter = new ApplyWhereHasLikeArray();

        $filter->apply($builder, ['field1', 'field2'], 'value', [
            'relationship' => 'relation',
            'is_or_where' => false,
            'is_deep_or_where' => false
        ]);

        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}

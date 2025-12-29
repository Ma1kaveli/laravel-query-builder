<?php

namespace Tests\Unit\Filters\Combine;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Mockery;
use QueryBuilder\Filters\Combine\ApplyWhereHasLike;
use Tests\TestCase;

class ApplyWhereHasLikeTest extends TestCase
{
    /**
     * Проверка, что ApplyWhereHasLike::apply() вызывает whereHas() с правильными параметрами
     */
    public function test_apply_calls_whereHas_with_like()
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
                '%value%'
            );

        $filter = new ApplyWhereHasLike();

        $filter->apply($builder, 'field1', 'value', [
            'relationship' => 'relation',
            'is_or_where' => false,
        ]);

        $this->assertTrue(true);
    }

    /**
     * Проверка, что ApplyWhereHasLike::apply() вызывает orWhereHas() с правильными параметрами
     */
    public function test_apply_calls_orWhereHas_with_like()
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
                '%value%'
            );

        $filter = new ApplyWhereHasLike();

        $filter->apply($builder, 'field1', 'value', [
            'relationship' => 'relation',
            'is_or_where' => true,
        ]);

        $this->assertTrue(true);
    }

    /**
     * Проверка, что ApplyWhereHasLike::apply() возвращает null, если передано не валидное значение
     */
    public function test_is_empty()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = new ApplyWhereHasLike();

        $result = $filter->apply($builder, 'field1', ['value', 'dfs'], [
            'relationship' => 'relation',
            'is_or_where' => true,
        ]);

        $this->assertNull($result);
    }
}

<?php

namespace Tests\Unit\Filters\Combine;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Combine\ApplyWhereHasWhereIn;
use Tests\TestCase;

class ApplyWhereHasWhereInTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldNotReceive('when');
        $builder->shouldNotReceive('whereHas');
        $builder->shouldNotReceive('orWhereHas');

        $filter = new ApplyWhereHasWhereIn();

        $filter->apply($builder, ['field'], ['a', 'b'], [
            'relationship' => 'relation',
            'is_or_where' => false,
        ]);

        $this->assertTrue(true);
    }

    /**
     * value не массив → приводится к массиву
     * whereHas + whereIn
     */
    public function test_apply_wraps_value_into_array_if_not_array()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $relatedBuilder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('when')
            ->once()
            ->with(false, Mockery::type('Closure'), Mockery::type('Closure'))
            ->andReturnUsing(fn ($c, $t, $f) => $f($builder));

        $builder->shouldReceive('whereHas')
            ->once()
            ->with('relation', Mockery::on(function ($closure) use ($relatedBuilder) {
                $closure($relatedBuilder);
                return true;
            }));

        $relatedBuilder->shouldReceive('whereIn')
            ->once()
            ->with('field', ['value']);

        $filter = new ApplyWhereHasWhereIn();

        $filter->apply($builder, 'field', 'value', [
            'relationship' => 'relation',
            'is_or_where' => false,
        ]);

        $this->assertTrue(true);
    }

    /**
     * value массив → передаётся как есть
     * whereHas + whereIn
     */
    public function test_apply_calls_whereHas_with_whereIn()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $relatedBuilder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('when')
            ->once()
            ->with(false, Mockery::type('Closure'), Mockery::type('Closure'))
            ->andReturnUsing(fn ($c, $t, $f) => $f($builder));

        $builder->shouldReceive('whereHas')
            ->once()
            ->with('relation', Mockery::on(function ($closure) use ($relatedBuilder) {
                $closure($relatedBuilder);
                return true;
            }));

        $relatedBuilder->shouldReceive('whereIn')
            ->once()
            ->with('field', ['a', 'b', 'c']);

        $filter = new ApplyWhereHasWhereIn();

        $filter->apply($builder, 'field', ['a', 'b', 'c'], [
            'relationship' => 'relation',
            'is_or_where' => false,
        ]);

        $this->assertTrue(true);
    }

    /**
     * is_or_where = true
     * → orWhereHas + whereIn
     */
    public function test_apply_calls_orWhereHas_with_whereIn()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $relatedBuilder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('when')
            ->once()
            ->with(true, Mockery::type('Closure'), Mockery::type('Closure'))
            ->andReturnUsing(fn ($c, $t, $f) => $t($builder));

        $builder->shouldReceive('orWhereHas')
            ->once()
            ->with('relation', Mockery::on(function ($closure) use ($relatedBuilder) {
                $closure($relatedBuilder);
                return true;
            }));

        $relatedBuilder->shouldReceive('whereIn')
            ->once()
            ->with('field', ['x', 'y']);

        $filter = new ApplyWhereHasWhereIn();

        $filter->apply($builder, 'field', ['x', 'y'], [
            'relationship' => 'relation',
            'is_or_where' => true,
        ]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

<?php

namespace Tests\Unit\Filters\Combine;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Combine\ApplyWhereHasNull;
use Tests\TestCase;

class ApplyWhereHasNullTest extends TestCase
{
    /**
     * is_or_where = false
     * invert = false
     * → whereHas + whereNull
     */
    public function test_apply_calls_whereHas_with_whereNull()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $relatedBuilder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('when')
            ->once()
            ->with(false, Mockery::type('Closure'), Mockery::type('Closure'))
            ->andReturnUsing(function ($condition, $ifTrue, $ifFalse) use ($builder) {
                $ifFalse($builder);
                return $builder;
            });

        $builder->shouldReceive('whereHas')
            ->once()
            ->with('relation', Mockery::on(function ($closure) use ($relatedBuilder) {
                $closure($relatedBuilder);
                return true;
            }));

        $relatedBuilder->shouldReceive('when')
            ->once()
            ->with(false, Mockery::type('Closure'), Mockery::type('Closure'))
            ->andReturnUsing(function ($condition, $ifTrue, $ifFalse) use ($relatedBuilder) {
                $ifFalse($relatedBuilder);
                return $relatedBuilder;
            });

        $relatedBuilder->shouldReceive('whereNull')
            ->once()
            ->with('field');

        $filter = new ApplyWhereHasNull();

        $filter->apply($builder, 'field', null, [
            'relationship' => 'relation',
            'is_or_where' => false,
            'invert' => false,
        ]);

        $this->assertTrue(true);
    }

    /**
     * is_or_where = false
     * invert = true
     * → whereHas + whereNotNull
     */
    public function test_apply_calls_whereHas_with_whereNotNull()
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

        $relatedBuilder->shouldReceive('when')
            ->once()
            ->with(true, Mockery::type('Closure'), Mockery::type('Closure'))
            ->andReturnUsing(fn ($c, $t, $f) => $t($relatedBuilder));

        $relatedBuilder->shouldReceive('whereNotNull')
            ->once()
            ->with('field');

        $filter = new ApplyWhereHasNull();

        $filter->apply($builder, 'field', null, [
            'relationship' => 'relation',
            'is_or_where' => false,
            'invert' => true,
        ]);

        $this->assertTrue(true);
    }

    /**
     * is_or_where = true
     * invert = false
     * → orWhereHas + whereNull
     */
    public function test_apply_calls_orWhereHas_with_whereNull()
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

        $relatedBuilder->shouldReceive('when')
            ->once()
            ->with(false, Mockery::type('Closure'), Mockery::type('Closure'))
            ->andReturnUsing(fn ($c, $t, $f) => $f($relatedBuilder));

        $relatedBuilder->shouldReceive('whereNull')
            ->once()
            ->with('field');

        $filter = new ApplyWhereHasNull();

        $filter->apply($builder, 'field', null, [
            'relationship' => 'relation',
            'is_or_where' => true,
            'invert' => false,
        ]);

        $this->assertTrue(true);
    }

    /**
     * is_or_where = true
     * invert = true
     * → orWhereHas + whereNotNull
     */
    public function test_apply_calls_orWhereHas_with_whereNotNull()
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

        $relatedBuilder->shouldReceive('when')
            ->once()
            ->with(true, Mockery::type('Closure'), Mockery::type('Closure'))
            ->andReturnUsing(fn ($c, $t, $f) => $t($relatedBuilder));

        $relatedBuilder->shouldReceive('whereNotNull')
            ->once()
            ->with('field');

        $filter = new ApplyWhereHasNull();

        $filter->apply($builder, 'field', null, [
            'relationship' => 'relation',
            'is_or_where' => true,
            'invert' => true,
        ]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

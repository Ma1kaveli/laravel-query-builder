<?php

namespace Tests\Unit\Filters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Collection;
use Mockery;
use QueryBuilder\Filters\GetFinalizer;
use Tests\TestCase;

class GetFinalizerTest extends TestCase
{
    /**
     * get вызывается без опций
     */
    public function test_apply_calls_get_without_options(): void
    {
        $result = collect([1, 2, 3]);

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('get')
            ->once()
            ->with([])
            ->andReturn($result);

        $finalizer = new GetFinalizer();
        $response = $finalizer->apply($builder, []);

        $this->assertSame($result, $response);
    }

    /**
     * get вызывается с массивом колонок
     */
    public function test_apply_calls_get_with_columns(): void
    {
        $result = collect(['id', 'name']);

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('get')
            ->once()
            ->with(['id', 'name'])
            ->andReturn($result);

        $finalizer = new GetFinalizer();
        $response = $finalizer->apply($builder, ['id', 'name']);

        $this->assertSame($result, $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

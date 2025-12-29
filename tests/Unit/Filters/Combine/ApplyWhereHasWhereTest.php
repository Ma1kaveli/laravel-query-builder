<?php

namespace Tests\Unit\Filters\Combine;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Combine\ApplyWhereHasWhere;
use Tests\TestCase;

class ApplyWhereHasWhereTest extends TestCase
{
    /**
     * value не строка → фильтр должен выйти сразу
     */
    public function test_apply_returns_early_if_value_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        // Никаких вызовов быть не должно
        $builder->shouldNotReceive('when');
        $builder->shouldNotReceive('whereHas');
        $builder->shouldNotReceive('orWhereHas');

        $filter = new ApplyWhereHasWhere();

        $filter->apply($builder, 'field', ['not-string'], [
            'relationship' => 'relation',
            'is_or_where' => false,
        ]);

        $this->assertTrue(true);
    }

    /**
     * field не строка → фильтр должен выйти сразу
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldNotReceive('when');
        $builder->shouldNotReceive('whereHas');
        $builder->shouldNotReceive('orWhereHas');

        $filter = new ApplyWhereHasWhere();

        $filter->apply($builder, ['field'], 'value', [
            'relationship' => 'relation',
            'is_or_where' => false,
        ]);

        $this->assertTrue(true);
    }

    /**
     * is_or_where = false
     * → whereHas + where
     */
    public function test_apply_calls_whereHas_with_where()
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

        $relatedBuilder->shouldReceive('where')
            ->once()
            ->with('field', 'value');

        $filter = new ApplyWhereHasWhere();

        $filter->apply($builder, 'field', 'VALUE', [
            'relationship' => 'relation',
            'is_or_where' => false,
        ]);

        $this->assertTrue(true);
    }

    /**
     * is_or_where = true
     * → orWhereHas + where
     */
    public function test_apply_calls_orWhereHas_with_where()
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

        $relatedBuilder->shouldReceive('where')
            ->once()
            ->with('field', 'value');

        $filter = new ApplyWhereHasWhere();

        $filter->apply($builder, 'field', ' VALUE ', [
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

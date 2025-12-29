<?php
namespace Tests\Unit\Filters\String;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\String\ApplyFullTextSearch;
use Tests\TestCase;

class ApplyFullTextSearchTest extends TestCase
{
    /**
     * field не строка или массив строк → ранний return
     */
    public function test_apply_returns_early_if_field_invalid()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyFullTextSearch();
        $filter->apply($builder, null, 'search', []);

        $this->assertTrue(true);
    }

    /**
     * value не строка или пустая → ранний return
     */
    public function test_apply_returns_early_if_value_invalid()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyFullTextSearch();
        $filter->apply($builder, 'title', '', []);

        $this->assertTrue(true);
    }

    /**
     * AND логика по умолчанию с одним полем
     */
    public function test_apply_calls_whereRaw_with_and_logic_single_field()
    {
        $model = Mockery::mock();
        $model->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($model);

        $filter = Mockery::mock(ApplyFullTextSearch::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'title')
            ->andReturn('table.title');

        $builder->shouldReceive('where')
            ->once()
            ->with(Mockery::type('Closure'), null, null, 'and')
            ->andReturnUsing(function ($closure) {
                $subQ = Mockery::mock(EloquentBuilder::class);
                $subQ->shouldReceive('where')->once()->with(Mockery::type('Closure'))
                    ->andReturnUsing(function ($innerClosure) {
                        $innerSubQ = Mockery::mock(EloquentBuilder::class);
                        $innerSubQ->shouldReceive('whereRaw')
                            ->once()
                            ->with('MATCH(table.title) AGAINST(? IN BOOLEAN MODE)', 'test query');
                        $innerClosure($innerSubQ);
                    });
                $closure($subQ);
            });

        $filter->apply($builder, 'title', 'test+query', []);
        $this->assertTrue(true);
    }

    /**
     * OR логика с несколькими полями
     */
    public function test_apply_calls_whereRaw_with_or_logic_multiple_fields()
    {
        $model = Mockery::mock();
        $model->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($model);

        $filter = Mockery::mock(ApplyFullTextSearch::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->twice()
            ->andReturnValues(['table.title', 'table.content']);

        $builder->shouldReceive('where')
            ->once()
            ->with(Mockery::type('Closure'), null, null, 'or')
            ->andReturnUsing(function ($closure) {
                $subQ = Mockery::mock(EloquentBuilder::class);

                $subQ->shouldReceive('where')
                    ->once()
                    ->with(Mockery::type('Closure'))
                    ->andReturnUsing(function ($innerClosure) {
                        $innerSubQ = Mockery::mock(EloquentBuilder::class);
                        $innerSubQ->shouldReceive('whereRaw')
                            ->once()
                            ->with('MATCH(table.title) AGAINST(? IN BOOLEAN MODE)', 'search term');
                        $innerClosure($innerSubQ);
                    });

                $subQ->shouldReceive('orWhere')
                    ->once()
                    ->with(Mockery::type('Closure'))
                    ->andReturnUsing(function ($innerClosure) {
                        $innerSubQ = Mockery::mock(EloquentBuilder::class);
                        $innerSubQ->shouldReceive('whereRaw')
                            ->once()
                            ->with('MATCH(table.content) AGAINST(? IN BOOLEAN MODE)', 'search term');
                        $innerClosure($innerSubQ);
                    });

                $closure($subQ);
            });

        $filter->apply($builder, ['title', 'content'], 'search+term', ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

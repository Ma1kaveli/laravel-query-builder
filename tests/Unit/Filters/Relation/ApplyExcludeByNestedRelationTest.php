<?php

namespace Tests\Unit\Filters\Relation;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Relation\ApplyExcludeByNestedRelation;
use Tests\TestCase;

class ApplyExcludeByNestedRelationTest extends TestCase
{
    /**
     * Ранний return, если relation или nested_relation не строка
     */
    public function test_apply_returns_early_if_relation_or_nested_relation_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereDoesntHave');

        $filter = new ApplyExcludeByNestedRelation();
        $filter->apply($builder, null, null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка whereDoesntHave с AND логикой
     */
    public function test_apply_calls_whereDoesntHave()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('whereDoesntHave')->once()->with('relation', Mockery::type('Closure'));

        $filter = new ApplyExcludeByNestedRelation();
        $filter->apply($builder, null, null, [
            'relation' => 'relation',
            'nested_relation' => 'nested',
        ]);
        $this->assertTrue(true);
    }

    /**
     * Проверка orWhereDoesntHave с OR логикой
     */
    public function test_apply_calls_orWhereDoesntHave()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('orWhereDoesntHave')->once()->with('relation', Mockery::type('Closure'));

        $filter = new ApplyExcludeByNestedRelation();
        $filter->apply($builder, null, null, [
            'relation' => 'relation',
            'nested_relation' => 'nested',
            'is_or_where' => true
        ]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

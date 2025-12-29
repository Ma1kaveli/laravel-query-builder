<?php

namespace Tests\Unit\Filters\Combine;

use Tests\TestCase;
use Mockery;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use QueryBuilder\Filters\Combine\ApplyCrossUponCrossWhereHasWhere;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyCrossUponCrossWhereHasWhereTest extends TestCase
{
    /**
     * Проверяет работу фильтра, когда оба массива term_1 и term_2 заполнены.
     * Должен вызваться внешний where с замыканием, внутри которого обрабатываются все комбинации term_1 * term_2
     */
    public function test_apply_both_terms()
    {
        $term1 = ['a', 'b'];
        $term2 = ['x', 'y'];

        $relationship1 = 'rel1';
        $relationship2 = 'rel2';
        $field1 = 'field1';
        $field2 = 'field2';

        $builder = Mockery::mock(EloquentBuilder::class);

        // Мок вложенного where
        $builder->shouldReceive('where')->once()->withArgs(function ($callback) {
            $subQuery = Mockery::mock(EloquentBuilder::class);
            $subQuery->shouldReceive('where')->andReturnSelf();
            $subQuery->shouldReceive('whereHas')->andReturnSelf();

            $callback($subQuery);

            return true;
        });

        $filter = new ApplyCrossUponCrossWhereHasWhere();
        $filter->apply($builder, null, null, [
            'is_or_where' => false,
            'term_1' => $term1,
            'term_2' => $term2,
            'relationship_1' => $relationship1,
            'relationship_2' => $relationship2,
            'field_1' => $field1,
            'field_2' => $field2,
        ]);

        $this->assertTrue(true);
    }

    /**
     * Проверяет работу фильтра, когда заполнен только массив term_1.
     * Должен вызваться внешний where с замыканием, внутри которого вызывается whereHas для relationship_1.
     */
    public function test_apply_only_term1()
    {
        $term1 = ['a', 'b'];

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('where')
            ->once()
            ->withArgs(function ($callback, $a = null, $b = null, $boolean = 'and') {
                $subQuery = Mockery::mock(EloquentBuilder::class);
                $subQuery->shouldReceive('whereHas')
                    ->once()
                    ->andReturnSelf();
                $callback($subQuery);
                return true;
            });

        $filter = new ApplyCrossUponCrossWhereHasWhere();
        $filter->apply($builder, null, null, [
            'is_or_where' => false,
            'term_1' => $term1,
            'term_2' => [],
            'relationship_1' => 'rel1',
            'relationship_2' => 'rel2',
            'field_1' => 'field1',
            'field_2' => 'field2',
        ]);

        $this->assertTrue(true);
    }

    /**
     * Проверяет работу фильтра, когда заполнен только массив term_2.
     * Должен вызваться внешний where с замыканием, внутри которого вызывается whereHas для relationship_2.
     */
    public function test_apply_only_term2()
    {
        $term2 = ['x', 'y'];

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('where')
            ->once()
            ->withArgs(function ($callback, $a = null, $b = null, $boolean = 'and') {
                $subQuery = Mockery::mock(EloquentBuilder::class);
                $subQuery->shouldReceive('whereHas')
                    ->once()
                    ->andReturnSelf();
                $callback($subQuery);
                return true;
            });

        $filter = new ApplyCrossUponCrossWhereHasWhere();
        $filter->apply($builder, null, null, [
            'is_or_where' => false,
            'term_1' => [],
            'term_2' => $term2,
            'relationship_1' => 'rel1',
            'relationship_2' => 'rel2',
            'field_1' => 'field1',
            'field_2' => 'field2',
        ]);

        $this->assertTrue(true);
    }

    /**
     * Проверяет работу фильтра, когда оба массива term_1 и term_2 пусты.
     * В этом случае where не должен вызываться.
     */
    public function test_apply_nothing_for_empty_terms()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('where')->never();

        $filter = new ApplyCrossUponCrossWhereHasWhere();
        $filter->apply($builder, null, null, [
            'is_or_where' => false,
            'term_1' => [],
            'term_2' => [],
            'relationship_1' => 'rel1',
            'relationship_2' => 'rel2',
            'field_1' => 'field1',
            'field_2' => 'field2',
        ]);

        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}

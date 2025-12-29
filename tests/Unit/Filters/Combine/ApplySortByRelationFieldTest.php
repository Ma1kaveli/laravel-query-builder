<?php

namespace Tests\Unit\Filters\Combine;

use Tests\TestCase;
use Mockery;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use QueryBuilder\Filters\Combine\ApplySortByRelationField;

class ApplySortByRelationFieldTest extends TestCase
{
    /**
     * Ничего не делает, если sort_by не разрешён
     */
    public function test_apply_does_nothing_if_sort_key_not_allowed()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('join')->never();
        $builder->shouldReceive('orderBy')->never();
        $builder->shouldReceive('select')->never();

        $availableSorts = (object) [
            'availableSorts' => [
                (object) ['sortByKey' => 'name'],
            ]
        ];

        $filter = new ApplySortByRelationField();

        $filter->apply($builder, null, null, [
            'sort_by' => 'price', // ❌ нет в availableSorts
            'descending' => 'false',
            'available_sorts' => $availableSorts,
            'columns' => ['*'],
            'owner_table' => 'products',
        ]);

        $this->assertTrue(true);
    }

    /**
     * Делает join + orderBy + select при валидном sort_by (ASC)
     */
    public function test_apply_adds_join_and_order_by_asc()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('join')
            ->once()
            ->with(
                'categories',
                'categories.id',
                '=',
                'products.category_id'
            )
            ->andReturnSelf();

        $builder->shouldReceive('orderBy')
            ->once()
            ->with('name', 'asc')
            ->andReturnSelf();

        $builder->shouldReceive('select')
            ->once()
            ->with(['products.*']);

        $availableSorts = (object) [
            'availableSorts' => [
                (object) [
                    'sortByKey' => 'name',
                    'relationTable' => 'categories',
                    'foreignKey' => 'id',
                    'ownerKey' => 'category_id',
                ]
            ]
        ];

        $filter = new ApplySortByRelationField();

        $filter->apply($builder, null, null, [
            'sort_by' => 'name',
            'descending' => 'false',
            'available_sorts' => $availableSorts,
            'columns' => ['products.*'],
            'owner_table' => 'products',
        ]);
    }

    /**
     * Делает desc, если descending = "true"
     */
    public function test_apply_orders_desc_when_descending_true()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('join')->once()->andReturnSelf();

        $builder->shouldReceive('orderBy')
            ->once()
            ->with('price', 'desc')
            ->andReturnSelf();

        $builder->shouldReceive('select')->once();

        $availableSorts = (object) [
            'availableSorts' => [
                (object) [
                    'sortByKey' => 'price',
                    'relationTable' => 'prices',
                    'foreignKey' => 'id',
                    'ownerKey' => 'price_id',
                ]
            ]
        ];

        $filter = new ApplySortByRelationField();

        $filter->apply($builder, null, null, [
            'sort_by' => 'price',
            'descending' => 'true',
            'available_sorts' => $availableSorts,
            'columns' => ['*'],
            'owner_table' => 'products',
        ]);
    }

    /**
     * descending не строка → сортировка ASC
     */
    public function test_apply_uses_asc_if_descending_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('join')->once()->andReturnSelf();

        $builder->shouldReceive('orderBy')
            ->once()
            ->with('name', 'asc')
            ->andReturnSelf();

        $builder->shouldReceive('select')->once();

        $availableSorts = (object) [
            'availableSorts' => [
                (object) [
                    'sortByKey' => 'name',
                    'relationTable' => 'users',
                    'foreignKey' => 'id',
                    'ownerKey' => 'user_id',
                ]
            ]
        ];

        $filter = new ApplySortByRelationField();

        $filter->apply($builder, null, null, [
            'sort_by' => 'name',
            'descending' => true,
            'available_sorts' => $availableSorts,
            'columns' => ['*'],
            'owner_table' => 'orders',
        ]);
    }

}

<?php

namespace Tests\Unit\Resources;

use QueryBuilder\Resources\PaginatedCollection;
use Tests\TestCase;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;

class PaginatedCollectionTest extends TestCase
{
    /**
     * Базовый тест для проверки того, что в пагинации есть нужные поля
     */
    public function test_it_maps_pagination_fields_correctly()
    {
        $items = collect([
            ['id' => 1],
            ['id' => 2],
        ]);

        $paginator = new LengthAwarePaginator(
            $items,
            total: 10,
            perPage: 2,
            currentPage: 1,
            options: [
                'path' => 'http://example.test',
            ]
        );

        $resourceData = [
            ['id' => 1],
            ['id' => 2],
        ];

        Config::set('query-builder.pagination_map', [
            'current_page' => 'current_page',
            'last_page' => 'last_page',
            'per_page' => 'per_page',
            'total' => 'total',
        ]);

        $collection = new PaginatedCollection($paginator, $resourceData);

        $result = $collection->toArray(request());

        $this->assertEquals($resourceData, $result['data']);
        $this->assertEquals(1, $result['current_page']);
        $this->assertEquals(5, $result['last_page']);
        $this->assertEquals(2, $result['per_page']);
        $this->assertEquals(10, $result['total']);
    }
}

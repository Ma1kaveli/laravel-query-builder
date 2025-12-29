<?php

namespace Tests\Unit\DTO;

use QueryBuilder\DTO\AvailableSort;
use QueryBuilder\DTO\AvailableSorts;

use Tests\TestCase;

class AvailableSortsTest extends TestCase
{
    /**
     * Базовый тест создания экземпляра
     */
    public function test_can_be_created(): void
    {
        $availableSorts = new AvailableSorts([
            new AvailableSort('sortByKey', 'relationTable', 'foreignKey', 'ownerKey')
        ]);

        $this->assertInstanceOf(
            AvailableSorts::class,
            $availableSorts
        );
    }

    /**
     * Проверка доступности сортировки
     */
    public function test_has_available_sort(): void
    {
        $availableSorts = new AvailableSorts([
            new AvailableSort('sortByKey', 'relationTable', 'foreignKey', 'ownerKey')
        ]);

        $this->assertInstanceOf(
            AvailableSort::class,
            $availableSorts->availableSorts[0]
        );
    }
}

<?php

namespace Tests\Unit\DTO;

use QueryBuilder\DTO\AvailableSort;
use Tests\TestCase;

class AvailableSortTest extends TestCase
{
    /**
     * создаем экземпляр класса
    */
    public function test_can_be_created(): void
    {
        $availableSort = new AvailableSort(
            'sortByKey',
            'relationTable',
            'foreignKey',
            'ownerKey',
        );

        $this->assertInstanceOf(
            AvailableSort::class,
            $availableSort
        );
    }

    /**
     * получаем sortByKey
    */
    public function test_can_get_sort_by_key(): void
    {
        $availableSort = new AvailableSort(
            'sortByKey',
            'relationTable',
            'foreignKey',
            'ownerKey',
        );

        $this->assertEquals(
            'sortByKey',
            $availableSort->sortByKey
        );
    }

    /**
     * получаем relationTable
    */
    public function test_can_get_relation_table(): void
    {
        $availableSort = new AvailableSort(
            'sortByKey',
            'relationTable',
            'foreignKey',
            'ownerKey',
        );

        $this->assertEquals(
            'relationTable',
            $availableSort->relationTable
        );
    }

    /**
     * получаем foreignKey
    */
    public function test_can_get_foreign_key(): void
    {
        $availableSort = new AvailableSort(
            'sortByKey',
            'relationTable',
            'foreignKey',
            'ownerKey',
        );

        $this->assertEquals(
            'foreignKey',
            $availableSort->foreignKey
        );
    }

    /**
     * получаем ownerKey
    */
    public function test_can_get_owner_key(): void
    {
        $availableSort = new AvailableSort(
            'sortByKey',
            'relationTable',
            'foreignKey',
            'ownerKey',
        );

        $this->assertEquals(
            'ownerKey',
            $availableSort->ownerKey
        );
    }
}

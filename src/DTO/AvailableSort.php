<?php

namespace BaseQueryBuilder\DTO;

class AvailableSort
{
    /**
     * @var string
     */
    public readonly string $sortByKey;

    /**
     * @var string
     */
    public readonly string $relationTable;

    /**
     * @var string
     */
    public readonly string $foreignKey;

    /**
     * @var string
     */
    public readonly string $ownerKey;

    /**
     * __construct
     *
     * @param string $sortByKey
     * @param string $relationTable
     * @param string $relationTable
     * @param string $ownerKey
     *
     * @return void
     */
    public function __construct(
        string $sortByKey,
        string $relationTable,
        string $foreignKey,
        string $ownerKey,
    ) {
        $this->sortByKey = $sortByKey;
        $this->relationTable = $relationTable;
        $this->foreignKey = $foreignKey;
        $this->ownerKey = $ownerKey;
    }
}

<?php

namespace QueryBuilder\Filters\System;

use QueryBuilder\Helpers\CheckTypes;
use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplySortBy implements FilterInterface
{
    use GetTableField;

    /**
     * apply
     *
     * @param EloquentQueryBuilder|QueryBuilder $query
     * @param string|array|null $field = null
     * @param mixed $value
     * @param mixed $options = []
     *
     * @return void
     */
    public function apply(
        EloquentQueryBuilder|QueryBuilder $query,
        string|array|null $field = null,
        mixed $value,
        mixed $options = []
    ): void {
        $sortBy = $options['sort_by'];
        $descending = $options['descending'];
        $availableSorts = $options['available_sorts'];
        $defaultField = $options['default_field'];

        $isAvailableSortBy = in_array($sortBy, $availableSorts);

        if (!CheckTypes::isString($sortBy) || ($isAvailableSortBy && ($defaultField === null))) {
            return;
        }

        $direction = (!CheckTypes::isString($descending) || $descending === 'false')
            ? 'asc' : 'desc';

        if (!$isAvailableSortBy) {
            $sortBy = $defaultField;
        }

        $query->orderBy($sortBy, $direction);
    }
}

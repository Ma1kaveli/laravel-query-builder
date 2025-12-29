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
        $sortBy = $options['sort_by'] ?? null;
        $descending = $options['descending'] ?? 'false';
        $availableSorts = $options['available_sorts'] ?? [];
        $defaultField = $options['default_field'] ?? null;

        if (!CheckTypes::isString($sortBy) && $sortBy !== null) {
            return;
        }

        if ($sortBy === null) {
            if (!CheckTypes::isString($defaultField)) {
                return;
            }
            $sortBy = $defaultField;
        }

        $isAvailable = in_array($sortBy, $availableSorts, true);

        if (!$isAvailable) {
            if (!CheckTypes::isString($defaultField)) {
                return;
            }
            $sortBy = $defaultField;
        }

        $direction = ($descending === 'true') ? 'desc' : 'asc';

        $query->orderBy($sortBy, $direction);
    }
}

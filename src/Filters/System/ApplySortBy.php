<?php

namespace BaseQueryBuilder\Filters\System;

use BaseQueryBuilder\Helpers\CheckTypes;
use BaseQueryBuilder\Interfaces\FilterInterface;
use BaseQueryBuilder\Traits\GetTableField;

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

        if (!CheckTypes::isString($sortBy)) {
            return;
        }

        $direction = (!CheckTypes::isString($descending) || $descending === 'false')
            ? 'asc' : 'desc';

        if (!in_array($sortBy, $availableSorts)) {
            $sortBy = $defaultField;
        }

        $query->orderBy($sortBy, $direction);
    }
}

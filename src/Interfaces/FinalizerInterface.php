<?php

namespace BaseQueryBuilder\Interfaces;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FinalizerInterface
{
    /**
     * apply
     *
     * @param EloquentQueryBuilder|QueryBuilder $query
     * @param array|string $options = ['*']
     *
     * @return array|EloquentCollection|SupportCollection|LengthAwarePaginator
     */
    public function apply(
        EloquentQueryBuilder|QueryBuilder $query,
        array|string $options = [],
    ): array|EloquentCollection|SupportCollection|LengthAwarePaginator;
}

<?php

namespace LaravelQueryBuilder\Filters;

use LaravelQueryBuilder\Interfaces\FinalizerInterface;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class GetFinalizer implements FinalizerInterface
{
    /**
     * apply
     *
     * @param EloquentQueryBuilder|QueryBuilder $query
     * @param array|string $options = []
     *
     * @return array|EloquentCollection|SupportCollection
     */
    public function apply(
        EloquentQueryBuilder|QueryBuilder $query,
        array|string $options = [],
    ): array|EloquentCollection|SupportCollection {
        return $query->get($options);
    }
}

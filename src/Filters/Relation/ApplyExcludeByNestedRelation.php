<?php

namespace QueryBuilder\Filters\Relation;

use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyExcludeByNestedRelation implements FilterInterface
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
        mixed $options = [],
    ): void {
        if (!$value) {
            return;
        }

        $relation       = $options['relation'];
        $nestedRelation = $options['nested_relation'];
        $isOrWhere      = $options['is_or_where'] ?? false;

        if (!$relation || !$nestedRelation) {
            return;
        }

        $method = $isOrWhere ? 'orWhereDoesntHave' : 'whereDoesntHave';

        $query->{$method}($relation, function ($q) use ($nestedRelation) {
            $q->whereHas($nestedRelation);
        });
    }
}


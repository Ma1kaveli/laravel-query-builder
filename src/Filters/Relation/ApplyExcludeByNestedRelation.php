<?php

namespace QueryBuilder\Filters\Relation;

use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;
use QueryBuilder\Helpers\CheckTypes;

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
        $relation       = $options['relation'] ?? null;
        $nestedRelation = $options['nested_relation'] ?? null;
        $isOrWhere      = $options['is_or_where'] ?? false;

        if (!CheckTypes::isString($relation) || !CheckTypes::isString($nestedRelation)) {
            return;
        }

        $method = $isOrWhere ? 'orWhereDoesntHave' : 'whereDoesntHave';

        $query->{$method}($relation, function ($q) use ($nestedRelation) {
            $q->whereHas($nestedRelation);
        });
    }
}


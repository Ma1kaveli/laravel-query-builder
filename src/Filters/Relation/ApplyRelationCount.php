<?php

namespace QueryBuilder\Filters\Relation;

use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;
use QueryBuilder\Helpers\CheckTypes;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use QueryBuilder\Constants\Operators;

class ApplyRelationCount implements FilterInterface
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
        if (!CheckTypes::isString($field)) {
            return;
        }

        if (!method_exists($query->getModel(), $field)) return;

        $operator = $options['operator'] ?? '>=';
        $count = $options['count'] ?? 1;

        if (!in_array($operator, Operators::AVAILABLE)
            || !CheckTypes::isInteger($count)
        ) {
            return;
        }

        $query->has($field, $operator, $count);
    }
}

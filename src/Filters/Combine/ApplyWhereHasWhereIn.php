<?php

namespace QueryBuilder\Filters\Combine;

use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyWhereHasWhereIn implements FilterInterface
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
        if (!is_array($value)) {
            $value = [$value];
        }

        $isOrWhere = $options['is_or_where'];
        $relationship = $options['relationship'];

        $query->when(
            $isOrWhere,
            fn ($q) => $q->orWhereHas($relationship, fn ($rQ) => $q->whereIn($field, $value)),
            fn ($q) => $q->whereHas($relationship, fn ($rQ) => $q->whereIn($field, $value)),
        );
    }
}

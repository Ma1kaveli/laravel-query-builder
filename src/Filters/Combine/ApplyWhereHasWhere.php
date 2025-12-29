<?php

namespace QueryBuilder\Filters\Combine;

use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use QueryBuilder\Helpers\CheckTypes;

class ApplyWhereHasWhere implements FilterInterface
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
        if (!CheckTypes::isString($value) || !CheckTypes::isString($field)) {
            return;
        }

        $value = trim(strtolower($value));

        $isOrWhere = $options['is_or_where'] ?? false;
        $relationship = $options['relationship'];

        $query->when(
            $isOrWhere,
            fn ($q) => $q->orWhereHas($relationship, fn ($rQ) => $rQ->where($field, $value)),
            fn ($q) => $q->whereHas($relationship, fn ($rQ) => $rQ->where($field, $value)),
        );
    }
}

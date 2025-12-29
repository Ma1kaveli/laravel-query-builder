<?php

namespace QueryBuilder\Filters\Combine;

use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use QueryBuilder\Helpers\CheckTypes;

class ApplyDeepWhereHasWhere implements FilterInterface
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
        if (!CheckTypes::isString($value)) {
            return;
        }

        $isOrWhere = $options['is_or_where'] ?? false;
        $isDeepOrWhere = $options['is_deep_or_where'];
        $relationship = $options['relationship'];

        if (!CheckTypes::isArrayWithElements($field)) {
            $field = [$field];
        }

        $deepFunc = function ($q) use ($field, $value, $isDeepOrWhere) {
            foreach ($field as $key => $el) {
                $isFirst = $key === 0;

                $q->where(
                    $el,
                    $value,
                    null,
                    ($isDeepOrWhere && !$isFirst) ? 'or' : 'and'
                );
            }
        };

        $query->when(
            $isOrWhere,
            fn ($q) => $q->orWhereHas($relationship, fn ($rQ) => $deepFunc($rQ)),
            fn ($q) => $q->whereHas($relationship, fn ($rQ) => $deepFunc($rQ)),
        );
    }
}

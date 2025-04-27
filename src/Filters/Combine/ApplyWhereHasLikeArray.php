<?php

namespace QueryBuilder\Filters\Combine;

use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyWhereHasLikeArray implements FilterInterface
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
        if (!is_array($field)) {
            $field = [$field];
        }

        $isDeepOrWhere = $options['is_deep_or_where'];
        $isOrWhere = $options['is_or_where'];
        $relationship = $options['relationship'];

        $likeFunc = function ($q) use ($field, $isDeepOrWhere, $value) {
            foreach ($field as $key => $el) {
                $isFirst = $key === 0;

                $q->where(
                    DB::raw("LOWER({$el})"),
                    'LIKE',
                    "%{$value}%",
                    ($isDeepOrWhere && !$isFirst) ? 'or' : 'and'
                );
            }
        };

        $query->when(
            $isOrWhere,
            fn ($q) => $q->orWhereHas($relationship, fn ($rQ) => $likeFunc($rQ)),
            fn ($q) => $q->whereHas($relationship, fn ($rQ) => $likeFunc($rQ)),
        );
    }
}

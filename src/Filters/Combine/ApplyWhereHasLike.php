<?php

namespace QueryBuilder\Filters\Combine;

use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyWhereHasLike implements FilterInterface
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
        $value = trim(mb_strtolower($value));

        $isOrWhere = $options['is_or_where'];
        $relationship = $options['relationship'];

        $likeFunc = fn ($q) => $q->where(DB::raw("LOWER({$field})"), 'LIKE' ,"%{$value}%");

        $query->when(
            $isOrWhere,
            fn ($q) => $q->orWhereHas($relationship, fn ($rQ) => $likeFunc($rQ)),
            fn ($q) => $q->whereHas($relationship, fn ($rQ) => $likeFunc($rQ)),
        );
    }
}

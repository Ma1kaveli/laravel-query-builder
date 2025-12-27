<?php

namespace QueryBuilder\Filters\Combine;

use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyWhereHasNull implements FilterInterface
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
        $isOrWhere = $options['is_or_where'];
        $relationship = $options['relationship'];
        $invert = $options['invert'] ?? false;

        $query->when(
            $isOrWhere,
            fn ($q) => $q->orWhereHas(
                $relationship,
                fn ($rQ) => $rQ->when(
                    $invert,
                    fn ($rQ) => $rQ->whereNotNull($field),
                    fn ($rQ) => $rQ->whereNull($field),
                )
            ),
            fn ($q) => $q->whereHas(
                $relationship,
                fn ($rQ) => $rQ->when(
                    $invert,
                    fn ($rQ) => $rQ->whereNotNull($field),
                    fn ($rQ) => $rQ->whereNull($field),
                )
            ),
        );
    }
}

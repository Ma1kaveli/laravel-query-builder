<?php

namespace QueryBuilder\Filters\Combine;

use QueryBuilder\Helpers\CheckTypes;
use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyCrossUponCrossWhereHasWhere implements FilterInterface
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

        $term1 = $options['term_1'];
        $term2 = $options['term_2'];
        $relationship1 = $options['relationship_1'];
        $field1 = $options['field_1'];
        $field2 = $options['field_2'];
        $relationship2 = $options['relationship_2'];

        if (!CheckTypes::isArrayWithElements($term1)
                && !CheckTypes::isArrayWithElements($term2)
        ) {
            return;
        }

        if (CheckTypes::isArrayWithElements($term1)
                && CheckTypes::isArrayWithElements($term2)
        ) {
            $query->where(
                function ($q) use (
                    $term1, $term2,
                    $relationship1, $relationship2,
                    $field1, $field2
                ): void {
                    $key = 0;
                    foreach ($term1 as $el1) {
                        foreach ($term2 as $el2) {
                            $isFirst = $key === 0;

                            $q->where(
                                fn ($subQ) => $subQ->whereHas(
                                    $relationship1,
                                    fn ($rQ) => $rQ->where($field1, $el1),
                                )->whereHas(
                                    $relationship2,
                                    fn ($rQ) => $rQ->where($field2, $el2),
                                ),
                                $isFirst ? 'and' : 'or'
                            );

                            $key++;
                        }
                    }
                },
                null,
                null,
                $isOrWhere ? 'or' : 'and'
            );

            return;
        }

        if (CheckTypes::isArrayWithElements($term1)) {
            $query->where(
                fn ($q) => $q->whereHas(
                    $relationship1,
                    fn ($rQ) => $rQ->whereIn($field1, $term1)
                ),
                null,
                null,
                $isOrWhere ? 'or' : 'and'
            );

            return;
        }

        $query->where(
            fn ($q) => $q->whereHas(
                $relationship2,
                fn ($rQ) => $rQ->whereIn($field2, $term2)
            ),
            null,
            null,
            $isOrWhere ? 'or' : 'and'
        );
    }
}

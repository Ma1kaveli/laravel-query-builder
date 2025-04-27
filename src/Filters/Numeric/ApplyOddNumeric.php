<?php

namespace QueryBuilder\Filters\Numeric;

use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyOddNumeric implements FilterInterface
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
        $fieldWithTable = $this->getFieldWithTable($query, $field);
        $isOrWhere = $options['is_or_where'];

        $query->whereRaw(
            "MOD($fieldWithTable, 2) = 1",
            [],
            $isOrWhere ? 'or' : 'and'
        );
    }
}

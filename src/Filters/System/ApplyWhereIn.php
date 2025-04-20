<?php

namespace BaseQueryBuilder\Filters\System;

use BaseQueryBuilder\Interfaces\FilterInterface;
use BaseQueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyWhereIn implements FilterInterface
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

        if (!is_array($value)) {
            $value = [$value];
        }

        $query->whereIn(
            $this->getFieldWithTable($query, $field),
            $value,
            $isOrWhere ? 'or' : 'and'
        );
    }
}

<?php

namespace QueryBuilder\Filters\System;

use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;
use QueryBuilder\Helpers\CheckTypes;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyLimit implements FilterInterface
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
        if (!CheckTypes::isInteger($value)) {
            return;
        }

        $query->limit($value);
    }
}

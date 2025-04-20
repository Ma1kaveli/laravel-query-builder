<?php

namespace LaravelQueryBuilder\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait GetTableField {

    /**
     * getFieldWithTable
     *
     * @param EloquentQueryBuilder|QueryBuilder|LengthAwarePaginator $query;
     * @param string $field
     *
     * @return string
     */
    protected function getFieldWithTable(
        EloquentQueryBuilder|QueryBuilder|LengthAwarePaginator $query,
        string $field
    ): string {
        return $query->getModel()->getTable().'.'.$field;
    }
}

<?php

namespace LaravelQueryBuilder\Filters\Logic;

use LaravelQueryBuilder\Interfaces\FilterInterface;
use LaravelQueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyEmpty implements FilterInterface
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
        mixed $options = [],
    ): void {
        $isOrWhere = $options['is_or_where'];

        $fieldWithTable = $this->getFieldWithTable($query, $field);

        $query->where(function($q) use ($fieldWithTable) {
            $q->whereNull($fieldWithTable)
              ->orWhere($fieldWithTable, '')
              ->orWhereJsonLength($fieldWithTable, 0);
        }, null, null, $isOrWhere ? 'or' : 'and');
    }
}

<?php

namespace LaravelQueryBuilder\Filters\Datetime;

use LaravelQueryBuilder\Helpers\CheckTypes;
use LaravelQueryBuilder\Interfaces\FilterInterface;
use LaravelQueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyHour implements FilterInterface
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
        if (!CheckTypes::isHour($value)) {
            return;
        }

        $isOrWhere = $options['is_or_where'];

        $fieldWithTable = $this->getFieldWithTable($query, $field);
        $query->whereRaw("HOUR({$fieldWithTable}) = ?", [ $value ], $isOrWhere ? 'or' : 'and');
    }
}

<?php

namespace LaravelQueryBuilder\Filters\Datetime;

use LaravelQueryBuilder\Helpers\CheckTypes;
use LaravelQueryBuilder\Interfaces\FilterInterface;
use LaravelQueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyArrayTime implements FilterInterface
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
        if (!CheckTypes::isTimeFormatArray($value)) {
            return;
        }

        $fieldWithTable = $this->getFieldWithTable($query, $field);
        $isOrWhere = $options['is_or_where'];

        $query->where(function ($q) use ($fieldWithTable, $value) {
            foreach ($value as $index => $time) {
                if ($index === 0) {
                    $q->whereTime($fieldWithTable, $time);
                } else {
                    $q->orWhere(function ($subQuery) use ($fieldWithTable, $time) {
                        $subQuery->whereTime($fieldWithTable, $time);
                    });
                }
            }
        }, null, null, $isOrWhere ? 'or' : 'and');
    }
}


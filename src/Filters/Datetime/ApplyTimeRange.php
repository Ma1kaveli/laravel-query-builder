<?php

namespace QueryBuilder\Filters\Datetime;

use QueryBuilder\Helpers\CheckTypes;
use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyTimeRange implements FilterInterface
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
        if (!CheckTypes::isTimeFormatRange($value) || !CheckTypes::isString($field)) {
            return;
        }

        $fieldWithTable = $this->getFieldWithTable($query, $field);
        $isOrWhere = $options['is_or_where'] ?? false;

        $query->where(function ($q) use ($fieldWithTable, $value) {
            $q->whereTime($fieldWithTable, '>=', reset($value))
                ->whereTime($fieldWithTable, '<=', end($value));
            },
            null, null, $isOrWhere ? 'or' : 'and'
        );
    }
}

<?php

namespace QueryBuilder\Filters\Datetime;

use QueryBuilder\Helpers\CheckTypes;
use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyDateNotRange implements FilterInterface
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
        if (!CheckTypes::isDateFormatRange($value)) {
            return;
        }

        $isOrWhere = $options['is_or_where'];

        $query->whereNotBetween(
            $this->getFieldWithTable($query, $field),
            $value,
            $isOrWhere ? 'or' : 'and'
        );
    }
}

<?php

namespace BaseQueryBuilder\Filters\Numeric;

use BaseQueryBuilder\Helpers\CheckTypes;
use BaseQueryBuilder\Interfaces\FilterInterface;
use BaseQueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyMultipleOf implements FilterInterface
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
        if (!CheckTypes::isNumeric($value) || $divisor = (int)$value === 0) {
            return;
        }

        $isOrWhere = $options['is_or_where'];
        $fieldWithTable = $this->getFieldWithTable($query, $field);

        $query->whereRaw(
            "MOD($fieldWithTable, ?) = 0",
            [$divisor],
            $isOrWhere ? 'or' : 'and'
        );
    }
}

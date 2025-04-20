<?php

namespace BaseQueryBuilder\Filters\Numeric;

use BaseQueryBuilder\Helpers\CheckTypes;
use BaseQueryBuilder\Interfaces\FilterInterface;
use BaseQueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyDouble implements FilterInterface
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
        if (!CheckTypes::isDouble($value)) {
            return;
        }

        $isOrWhere = $options['is_or_where'];

        $query->where(
            $this->getFieldWithTable($query, $field),
            (double) $value,
            null,
            $isOrWhere ? 'or' : 'and'
        );
    }
}

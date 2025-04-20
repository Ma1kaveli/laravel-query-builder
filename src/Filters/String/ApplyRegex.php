<?php

namespace BaseQueryBuilder\Filters\String;

use BaseQueryBuilder\Helpers\CheckTypes;
use BaseQueryBuilder\Interfaces\FilterInterface;
use BaseQueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyRegex implements FilterInterface
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
        if (!CheckTypes::isString($value) || $value === '') {
            return;
        }

        if (!preg_match('/^[a-z0-9~%\/:_\[\]()\*\-.]+$/i', $value)) {
            return;
        }

        $isOrWhere = $options['is_or_where'];

        $query->where(
            $this->getFieldWithTable($query, $field),
            'REGEXP',
            $value,
            $isOrWhere ? 'or' : 'and'
        );
    }
}

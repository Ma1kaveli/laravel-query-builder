<?php

namespace QueryBuilder\Filters\Logic;

use QueryBuilder\Helpers\CheckTypes;
use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyExistsByBoolean implements FilterInterface
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
        $filterableField = $options['filterable_field'] ?? null;
        $canBeNull       = $options['can_be_null'] ?? false;
        $isOrWhere       = $options['is_or_where'] ?? false;
        $invert          = $options['invert'] ?? false;

        if (!CheckTypes::isString($filterableField)) {
            return;
        }

        if ($value === null) {
            if (!$canBeNull) {
                return;
            }
        }

        if (!CheckTypes::isBool($value) && $value !== null) {
            return;
        }

        // Инверсия бизнес-смысла
        $exists = $invert ? !$value : $value;

        $method = $isOrWhere
            ? ($exists ? 'orWhereNotNull' : 'orWhereNull')
            : ($exists ? 'whereNotNull'   : 'whereNull');

        $query->{$method}(
            $this->getFieldWithTable($query, $filterableField)
        );
    }
}


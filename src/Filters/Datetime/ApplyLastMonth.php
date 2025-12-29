<?php

namespace QueryBuilder\Filters\Datetime;

use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Helpers\CheckTypes;
use QueryBuilder\Traits\GetTableField;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyLastMonth implements FilterInterface
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
        if (!CheckTypes::isString($field)) {
            return;
        }

        $subMonth = Carbon::now()->subMonth();
        $isOrWhere = $options['is_or_where'] ?? false;

        $query->whereBetween(
            $this->getFieldWithTable($query, $field),
            [$subMonth->startOfMonth(), $subMonth->endOfMonth()],
            $isOrWhere ? 'or' : 'and'
        );
    }
}

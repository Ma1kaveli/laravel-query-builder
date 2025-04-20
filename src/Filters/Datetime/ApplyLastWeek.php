<?php

namespace LaravelQueryBuilder\Filters\Datetime;

use LaravelQueryBuilder\Interfaces\FilterInterface;
use LaravelQueryBuilder\Traits\GetTableField;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyLastWeek implements FilterInterface
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
        $subWeek = Carbon::now()->subWeek();
        $isOrWhere = $options['is_or_where'];

        $query->whereBetween(
            $this->getFieldWithTable($query, $field),
            [$subWeek->startOfWeek(), $subWeek->endOfWeek()],
            $isOrWhere ? 'or' : 'and'
        );
    }
}

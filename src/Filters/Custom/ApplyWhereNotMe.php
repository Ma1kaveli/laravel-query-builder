<?php

namespace BaseQueryBuilder\Filters\Custom;

use BaseQueryBuilder\Interfaces\FilterInterface;
use BaseQueryBuilder\Traits\GetTableField;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyWhereNotMe implements FilterInterface
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
        $authUser = Auth::user();

        if (empty($authUser)) return;

        $query->where($this->getFieldWithTable($query, $field), '!=', $authUser->id);
    }
}

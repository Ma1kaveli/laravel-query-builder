<?php

namespace LaravelQueryBuilder\Filters\String;

use LaravelQueryBuilder\Helpers\CheckTypes;
use LaravelQueryBuilder\Interfaces\FilterInterface;
use LaravelQueryBuilder\Traits\GetTableField;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyLike implements FilterInterface
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

        $value = str_replace(
            ['%', '_'],
            ['\%', '\_'],
            trim(mb_strtolower($value))
        );

        if (!is_array($field)) {
            $field = [$field];
        }

        $isOrWhere = $options['is_or_where'];

        $query->where(function ($q) use ($value, $field, $query) {
            foreach ($field as $key => $el) {
                $method = $key === 0 ? 'where' : 'orWhere';
                $fieldWithTable = $this->getFieldWithTable($query, $el);

                $q->{$method}(function($subQ) use ($value, $fieldWithTable) {
                    $subQ->where(
                        DB::raw("LOWER({$fieldWithTable})"),
                        'LIKE',
                        "%{$value}%"
                    );
                });
            }
        }, null, null, $isOrWhere ? 'or' : 'and');
    }
}

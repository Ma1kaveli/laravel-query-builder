<?php

namespace QueryBuilder\Filters\String;

use QueryBuilder\Helpers\CheckTypes;
use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyFullTextSearch implements FilterInterface
{
    use GetTableField;
    /**
     * apply
     *
     * @param EloquentQueryBuilder|QueryBuilder $query
     * @param string|array|null $field = null
     * @param mixed $value = null
     * @param mixed $options = []
     *
     * @return void
     */
    public function apply(
        EloquentQueryBuilder|QueryBuilder $query,
        string|array|null $field = null,
        mixed $value = null,
        mixed $options = []
    ): void {
        if ( !( CheckTypes::isString($field) || CheckTypes::isStringArray($field) )
            || !CheckTypes::isString($value)
            || $value === ''
        ) {
            return;
        }
        $value = preg_replace(
            '/[+\-><\(\)~*\"@]+/',
            ' ',
            $value
        );
        if (!CheckTypes::isStringArray($field)) {
            $field = [$field];
        }
        $isOrWhere = $options['is_or_where'] ?? false;
        $query->where(function ($q) use ($value, $field, $query) {
            foreach ($field as $key => $el) {
                $method = $key === 0 ? 'where' : 'orWhere';
                $fieldWithTable = $this->getFieldWithTable($query, $el);
                $q->{$method}(function($subQ) use ($value, $fieldWithTable) {
                    $subQ->whereRaw(
                        "MATCH({$fieldWithTable}) AGAINST(? IN BOOLEAN MODE)",
                        $value
                    );
                });
            }
        }, null, null, $isOrWhere ? 'or' : 'and');
    }
}

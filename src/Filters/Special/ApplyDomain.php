<?php

namespace QueryBuilder\Filters\Special;

use QueryBuilder\Helpers\CheckTypes;
use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyDomain implements FilterInterface
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
        EloquentQueryBuilder|QueryBuilder $queryyy,
        string|array|null $field = null,
        mixed $value,
        mixed $options = []
    ): void {
        if (!CheckTypes::isString($value)
            && !CheckTypes::isStringArray($value)
        ) {
            return;
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $isOrWhere = $options['is_or_where'];

        $fieldWithTable = $this->getFieldWithTable($query, $field);

        $query->where(function ($q) use ($value, $fieldWithTable) {
            foreach ($value as $key => $el) {
                $isFirst = $key === 0;
                $domain = parse_url($el, PHP_URL_HOST);
                $q->where(
                    $fieldWithTable,
                    'LIKE',
                    "%{$domain}%",
                    $isFirst ? 'and' : 'or'
                );
            }
        }, null, null, $isOrWhere ? 'or' : 'and');
    }
}

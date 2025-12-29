<?php

namespace QueryBuilder\Filters\Special;

use QueryBuilder\Helpers\CheckTypes;
use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyIpAddress implements FilterInterface
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
        $ip = $options['ip'] ?? null;
        $cidr = $options['cidr'] ?? null;

        if (!CheckTypes::isString($ip)
            || !CheckTypes::isString($cidr)
            || !CheckTypes::isString($field)
        ) {
            return;
        }

        $isOrWhere = $options['is_or_where'] ?? false;

        $fieldWithTable = $this->getFieldWithTable($query, $field);

        $query->whereRaw(
            "INET6_ATON(?) BETWEEN INET6_ATON({$fieldWithTable}) AND INET6_ATON({$fieldWithTable}) + (1 << (128 - ?)) - 1",
            [$ip, $cidr],
            $isOrWhere ? 'or' : 'and'
        );
    }
}

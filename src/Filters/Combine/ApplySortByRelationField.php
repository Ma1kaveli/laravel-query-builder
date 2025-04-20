<?php

namespace BaseQueryBuilder\Filters\Combine;

use BaseQueryBuilder\Helpers\CheckTypes;
use BaseQueryBuilder\Interfaces\FilterInterface;
use BaseQueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplySortByRelationField implements FilterInterface
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
        $sortBy = $options['sort_by'];
        $descending = $options['descending'];
        $availableSorts = $options['available_sorts'];
        $columns = $options['columns'];
        $ownerTable = $options['owner_table'];

        $sortKeys = array_column($availableSorts->availableSorts, 'sortByKey');

        if (!in_array($sortBy, $sortKeys, true)) {
            return;
        }

        $index = array_search($sortBy, $sortKeys, true);

        $sortParams = $availableSorts->availableSorts[$index];

        $direction = (!CheckTypes::isString($descending) || $descending === 'false')
            ? 'asc' : 'desc';

        $query->join(
            $sortParams->relationTable,
            $sortParams->relationTable . '.' . $sortParams->foreignKey,
            '=',
            $ownerTable . '.' . $sortParams->ownerKey
        )->orderBy($sortBy, $direction)->select($columns);
    }
}

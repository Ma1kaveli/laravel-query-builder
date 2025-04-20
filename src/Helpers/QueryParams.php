<?php

namespace BaseQueryBuilder\Helpers;

class QueryParams
{

    /**
     * defaultPaginationParamsKeys
     *
     * @return array
     */
    public static function defaultPaginationParamsKeys(): array
    {
        return [
            'showDeleted',
            'rowsPerPage',
            'rowsNumber',
            'page',
            'sortBy'
        ];
    }

    /**
     * getSortParams
     *
     * @param array $params
     * @param array $productSortTypes
     * @return array
     */
    public static function getSortParams(array $params, array $sortTypes): array
    {
        $params['descending'] = !array_key_exists($params['sort_by'], $sortTypes)
            ? 'asc' : $sortTypes[$params['sort_by']]['descending'];

        $params['sort_by'] = !array_key_exists($params['sort_by'], $sortTypes)
            ? 'created_at' : $sortTypes[$params['sort_by']]['value'];

        return $params;
    }

    /**
     * convertArrStrToArrNumber
     *
     * @param array $params
     * @return array
     */
    public static function convertArrStrToArrNumber(array $params): array
    {
        if (empty($params)) {
            return $params;
        }

        if (!is_array($params) || count($params) < 1) {
            return [(int) $params];
        }

        foreach ($params as $key => $value) {
            $params[$key] = (int) $value;
        }

        return $params;
    }

    /**
     * mapSortBy
     *
     * @param  mixed $params
     * @param  mixed $mapper
     * @param  mixed $sortByKey
     * @return array
     */
    public static function mapSortBy($params, $mapper, $sortByKey = 'sort_by'): array
    {
        foreach ($params as $key => $value) {
            if (($key !== $sortByKey) || !is_string($value)) continue;

            if (!isset($mapper[$value])) continue;

            $params[$key] = $mapper[$value];
        }

        return $params;
    }
}

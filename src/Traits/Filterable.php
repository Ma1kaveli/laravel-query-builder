<?php

namespace QueryBuilder\Traits;

trait Filterable {
    /**
     * Применяет фильтры к запросу.
     *
     * @param array $params
     *
     * @return mixed
     */
    public static function filter(array $params): mixed
    {
        $query = static::query();
        $filterClass = static::getFilterClass();

        if (class_exists($filterClass)) {
            $filter = new $filterClass($query, $params);

            return $filter;
        }

        return $query;
    }

    /**
     * Возвращает имя класса фильтра
     *
     * @return string
     */
    abstract public static function getFilterClass(): string;
}

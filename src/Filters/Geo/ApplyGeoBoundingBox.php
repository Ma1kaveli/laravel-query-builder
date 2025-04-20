<?php

namespace LaravelQueryBuilder\Filters\Geo;

use LaravelQueryBuilder\Helpers\CheckTypes;
use LaravelQueryBuilder\Interfaces\FilterInterface;
use LaravelQueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyGeoBoundingBox implements FilterInterface
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
        $isOrWhere = $options['is_or_where'];

        $lonField = $options['lon_field'];
        $latField = $options['lat_field'];
        $minLon = $options['min_lon'];
        $minLat = $options['min_lat'];
        $maxLon = $options['max_lon'];
        $maxLat = $options['max_lat'];

        if (
            !CheckTypes::isString($minLon)
                || !CheckTypes::isString($minLat)
                || !CheckTypes::isString($maxLon)
                || !CheckTypes::isString($maxLat)
        ) {
            return;
        }

        $lonFieldWithTable = $this->getFieldWithTable($query, $lonField);
        $latFieldWithTable = $this->getFieldWithTable($query, $latField);

        $query->whereRaw(
            "MBRContains(
                ST_GeomFromText(?),
                ST_GeomFromText(CONCAT('POINT(', {$lonFieldWithTable}->>'$.lon', ' ', {$latFieldWithTable}->>'$.lat', ')'))
            )",
            ["LINESTRING({$minLon} {$minLat}, {$maxLon} {$maxLat})"],
            $isOrWhere ? 'or' : 'and'
        );
    }
}

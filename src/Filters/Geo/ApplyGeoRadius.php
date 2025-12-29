<?php

namespace QueryBuilder\Filters\Geo;

use QueryBuilder\Helpers\CheckTypes;
use QueryBuilder\Interfaces\FilterInterface;
use QueryBuilder\Traits\GetTableField;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyGeoRadius implements FilterInterface
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
        $isOrWhere = $options['is_or_where'] ?? false;

        $lonField = $options['lon_field'];
        $latField = $options['lat_field'];
        $lon = $options['lon'];
        $lat = $options['lat'];
        $radius = $options['radius'];

        if (
            !CheckTypes::isString($lonField)
                || !CheckTypes::isString($latField)
                || !CheckTypes::isString($lon)
                || !CheckTypes::isString($lat)
                || !CheckTypes::isString($radius)
        ) {
            return;
        }

        $lonFieldWithTable = $this->getFieldWithTable($query, $lonField);
        $latFieldWithTable = $this->getFieldWithTable($query, $latField);

        $query->whereRaw(
            "ST_Distance_Sphere(
                POINT(?, ?),
                POINT({$lonFieldWithTable}->>'$.lon', {$latFieldWithTable}->>'$.lat')
            ) <= ?",
            [$lon, $lat, $radius],
            $isOrWhere ? 'or' : 'and'
        );
    }
}

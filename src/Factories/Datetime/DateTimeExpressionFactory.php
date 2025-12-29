<?php

namespace QueryBuilder\Factories\Datetime;

use QueryBuilder\Builders\Datetime\MySqlDateTimeExpressionBuilder;
use QueryBuilder\Builders\Datetime\PostgresDateTimeExpressionBuilder;
use QueryBuilder\Interfaces\DateTimeExpressionBuilder;

class DateTimeExpressionFactory
{
    public static function make(): DateTimeExpressionBuilder
    {
        return match (config('database.default')) {
            'pgsql' => new PostgresDateTimeExpressionBuilder(),
            default => new MySqlDateTimeExpressionBuilder(),
        };
    }
}

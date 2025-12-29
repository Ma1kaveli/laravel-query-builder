<?php

namespace QueryBuilder\Builders\Datetime;

use QueryBuilder\Interfaces\DateTimeExpressionBuilder;

class PostgresDateTimeExpressionBuilder implements DateTimeExpressionBuilder
{
    public function hour(string $field): string
    {
        return "EXTRACT(HOUR FROM $field)";
    }

    public function minute(string $field): string
    {
        return "EXTRACT(MINUTE FROM $field)";
    }

    public function day(string $field): string
    {
        return "EXTRACT(DAY FROM $field)";
    }
}


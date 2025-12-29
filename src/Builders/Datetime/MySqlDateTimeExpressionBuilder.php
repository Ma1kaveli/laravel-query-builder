<?php

namespace QueryBuilder\Builders\Datetime;

use QueryBuilder\Interfaces\DateTimeExpressionBuilder;

class MySqlDateTimeExpressionBuilder implements DateTimeExpressionBuilder
{
    public function hour(string $field): string
    {
        return "HOUR($field)";
    }

    public function minute(string $field): string
    {
        return "MINUTE($field)";
    }

    public function day(string $field): string
    {
        return "DAY($field)";
    }
}

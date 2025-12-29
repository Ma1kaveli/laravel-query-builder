<?php

namespace QueryBuilder\Interfaces;

interface DateTimeExpressionBuilder
{
    public function hour(string $field): string;
    public function minute(string $field): string;
    public function day(string $field): string;
}


<?php

namespace Tests\Unit\DTO;

use Tests\TestCase;
use QueryBuilder\DTO\DeepWhereHasWhereParam;
use QueryBuilder\DTO\DeepWhereHasWhereParams;

class DeepWhereHasWhereParamsTest extends TestCase
{
    /**
     * Базовый тест
     */
    public function test_deep_where_has_where_params()
    {
        $params = new DeepWhereHasWhereParams([
            new DeepWhereHasWhereParam('posts', 'title'),
            new DeepWhereHasWhereParam('author', 'first_name')
        ]);
        $this->assertInstanceOf(DeepWhereHasWhereParams::class, $params);
    }

    /**
     * Тест с параметром
     */
    public function test_deep_where_has_where_params_with_param()
    {
        $params = new DeepWhereHasWhereParams([
            new DeepWhereHasWhereParam('posts', 'title'),
            new DeepWhereHasWhereParam('author', 'first_name', true, 'paramName')
        ]);
        $this->assertInstanceOf(DeepWhereHasWhereParams::class, $params);
    }
}

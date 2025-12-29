<?php

namespace Tests\Unit\DTO;

use QueryBuilder\DTO\CrossUponCrossParams;
use QueryBuilder\DTO\DeepWhereHasWhereParam;
use Tests\TestCase;

class CrossUponCrossParamsTest extends TestCase
{
    /**
     * Базовый тест
     */
    public function test_cross_upon_cross_params()
    {
        $params = new CrossUponCrossParams(
            new DeepWhereHasWhereParam('posts', 'title'),
            new DeepWhereHasWhereParam('author', 'first_name')
        );
        $this->assertInstanceOf(CrossUponCrossParams::class, $params);
    }

    /**
     * Тест с параметром
     */
    public function test_cross_upon_cross_params_with_param()
    {
        $params = new CrossUponCrossParams(
            new DeepWhereHasWhereParam('posts', 'title'),
            new DeepWhereHasWhereParam('author', 'first_name', true, 'paramName')
        );
        $this->assertInstanceOf(CrossUponCrossParams::class, $params);
    }

    /**
     * Тест с параметром
     */
    public function test_has_param()
    {
        $params = new CrossUponCrossParams(
            new DeepWhereHasWhereParam('posts', 'title'),
            new DeepWhereHasWhereParam('author', 'first_name', true, 'paramName')
        );

        $this->assertTrue($params->firstElement !== null);
    }
}

<?php

namespace Tests\Unit\DTO;

use Tests\TestCase;
use QueryBuilder\DTO\DeepWhereHasWhereParam;

class DeepWhereHasWhereParamTest extends TestCase
{
    /**
     * При создании базового DeepWhereHasWhereParam
     */
    public function test_deep_where_has_where_param(): void
    {
        $param = new DeepWhereHasWhereParam('posts', 'title');

        $this->assertEquals('posts', $param->relationship);
        $this->assertEquals('title', $param->field);
        $this->assertFalse($param->isDeepOrWhere);
        $this->assertNull($param->paramName);
    }

    /**
     * При создании DeepWhereHasWhereParam с isDeepOrWhere = true
     */
    public function test_deep_where_has_where_param_deep_or_where(): void
    {
        $param = new DeepWhereHasWhereParam('posts', 'title', true);

        $this->assertEquals('posts', $param->relationship);
        $this->assertEquals('title', $param->field);
        $this->assertTrue($param->isDeepOrWhere);
        $this->assertNull($param->paramName);
    }

    /**
     * При создании DeepWhereHasWhereParam с paramName
     */
    public function test_deep_where_has_where_param_with_param_name(): void
    {
        $param = new DeepWhereHasWhereParam('posts', 'title', true, 'paramName');

        $this->assertEquals('posts', $param->relationship);
        $this->assertEquals('title', $param->field);
        $this->assertTrue($param->isDeepOrWhere);
        $this->assertEquals('paramName', $param->paramName);
    }
}

<?php

namespace Tests\Unit\Traits;

use Tests\Fakes\FakeGetTableFieldClass;
use Tests\TestCase;

class GetTableFieldTest extends TestCase
{
    /**
     * Проверка работы метода getFieldWithTable в Трейте GetTableField
     */
    public function test_get_table_field_returns_correct_value()
    {
        $model = new FakeGetTableFieldClass();

        $expected = $model->getTable() . '.name';

        $this->assertEquals(
            $expected,
            $model->callGetFieldWithTable($model->newQuery(), 'name')
        );
    }
}

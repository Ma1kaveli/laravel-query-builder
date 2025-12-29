<?php

namespace Tests\Unit\Traits;

use Tests\Fakes\FakeFilterableClass;
use Tests\Fakes\FakeFilterableModel;
use Tests\TestCase;

class FilterableTest extends TestCase
{
    /**
     * Проверка трайта Filterable
     */
    public function test_filterable()
    {
        $class = new FakeFilterableModel();

        $this->assertEquals(FakeFilterableClass::class, $class->getFilterClass());
    }

    /**
     * Проверка трайта Filterable
     */
    public function test_filter()
    {
        $class = new FakeFilterableModel();

        $this->assertInstanceOf(FakeFilterableClass::class, $class->filter([]));
    }

    /**
     * Проверка трайта Filterable, что он прокидывает query во внутрь
     */
    public function test_has_query()
    {
        $class = new FakeFilterableModel();

        $this->assertNotNull($class->filter([])->getQuery());
    }

    /**
     * Проверка трайта Filterable, что он прокидывает параметры
     */
    public function test_has_parametr()
    {
        $class = new FakeFilterableModel();

        $this->assertNotNull(
            $class->filter(['test' => 1])->getParams()['test']
        );
    }
}

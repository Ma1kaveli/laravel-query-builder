<?php

namespace Tests\Fakes;

use QueryBuilder\BaseQueryBuilder;

class FakeFilterableClass extends BaseQueryBuilder {
    public function getQuery() {
        return $this->query;
    }

    public function getParams()
    {
        return $this->params;
    }
}

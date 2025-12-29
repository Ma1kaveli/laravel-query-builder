<?php

namespace Tests\Fakes;

use Illuminate\Database\Eloquent\Model;
use QueryBuilder\Traits\GetTableField;

class FakeGetTableFieldClass extends Model
{
    use GetTableField;

    protected $table = 'fake_table';

    public function callGetFieldWithTable($query, $field)
    {
        return $this->getFieldWithTable($query, $field);
    }
}

<?php

namespace Tests\Fakes;

use Illuminate\Database\Eloquent\Model;
use QueryBuilder\Traits\Filterable;

class FakeFilterableModel extends Model
{
    use Filterable;

    /**
     * Summary of getFilterClass
     * 
     * @return string
     */
    public static function getFilterClass(): string
    {
        return FakeFilterableClass::class;
    }
}

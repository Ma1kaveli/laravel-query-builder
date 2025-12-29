<?php

namespace Tests\Unit\Filters\Relation;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use QueryBuilder\Filters\Relation\ApplyHasRelation;
use Tests\TestCase;

class ApplyHasRelationTest extends TestCase
{
    /**
     * Ранний return если поле не строка
     */
    public function test_apply_returns_early_if_field_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('has');

        $filter = new ApplyHasRelation();
        $filter->apply($builder, null, null, []);
        $this->assertTrue(true);
    }

    /**
     * Ранний return если метод не существует в модели
     */
    public function test_apply_returns_early_if_method_not_exists()
    {
        $model = Mockery::mock(Model::class);
        $builder = Mockery::mock(EloquentBuilder::class)->makePartial();
        $builder->shouldReceive('getModel')->andReturn($model);
        $builder->shouldNotReceive('has');

        $filter = new ApplyHasRelation();
        $filter->apply($builder, 'relation', null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка вызова has
     */
    public function test_apply_calls_has_method()
    {
        $model = new class extends Model {
            public function relation() {}
        };

        $builder = Mockery::mock(EloquentBuilder::class)->makePartial();
        $builder->shouldReceive('getModel')->andReturn($model);
        $builder->shouldReceive('has')->once()->with('relation');

        $filter = new ApplyHasRelation();
        $filter->apply($builder, 'relation', null, []);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

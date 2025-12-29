<?php

namespace Tests\Unit\Filters\Relation;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use QueryBuilder\Filters\Relation\ApplyRelationCount;
use Tests\TestCase;

class ApplyRelationCountTest extends TestCase
{
    /**
     * Ранний return если поле не строка
     */
    public function test_apply_returns_early_if_field_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('has');

        $filter = new ApplyRelationCount();
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

        $filter = new ApplyRelationCount();
        $filter->apply($builder, 'relation', null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка вызова has с указанным оператором и count
     */
    public function test_apply_calls_has_with_operator_and_count()
    {
        $model = new class extends Model {
            public function relation() {}
        };

        $builder = Mockery::mock(EloquentBuilder::class)->makePartial();
        $builder->shouldReceive('getModel')->andReturn($model);
        $builder->shouldReceive('has')->once()->with('relation', '>=', 5);

        $filter = new ApplyRelationCount();
        $filter->apply($builder, 'relation', null, ['operator' => '>=', 'count' => 5]);
        $this->assertTrue(true);
    }

    /**
     * Проверка что неверный оператор → ранний return
     */
    public function test_apply_returns_early_if_invalid_operator()
    {
        $model = new class extends Model {
            public function relation() {}
        };

        $builder = Mockery::mock(EloquentBuilder::class)->makePartial();
        $builder->shouldReceive('getModel')->andReturn($model);
        $builder->shouldNotReceive('has');

        $filter = new ApplyRelationCount();
        $filter->apply($builder, 'relation', null, ['operator' => 'invalid', 'count' => 1]);
        $this->assertTrue(true);
    }

    /**
     * Проверка что count не integer → ранний return
     */
    public function test_apply_returns_early_if_count_not_integer()
    {
        $model = new class extends Model {
            public function relation() {}
        };

        $builder = Mockery::mock(EloquentBuilder::class)->makePartial();
        $builder->shouldReceive('getModel')->andReturn($model);
        $builder->shouldNotReceive('has');

        $filter = new ApplyRelationCount();
        $filter->apply($builder, 'relation', null, ['operator' => '>=', 'count' => 'abc']);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

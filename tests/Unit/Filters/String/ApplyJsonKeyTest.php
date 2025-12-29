<?php
namespace Tests\Unit\Filters\String;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\String\ApplyJsonKey;
use Tests\TestCase;

class ApplyJsonKeyTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereNotNull');
        $filter = new ApplyJsonKey();
        $filter->apply($builder, null, 'key', []);
        $this->assertTrue(true);
    }

    /**
     * value не строка или пустая → ранний return
     */
    public function test_apply_returns_early_if_value_invalid()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereNotNull');
        $filter = new ApplyJsonKey();
        $filter->apply($builder, 'data', '', []);
        $this->assertTrue(true);
    }

    /**
     * AND логика по умолчанию
     */
    public function test_apply_calls_whereNotNull_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('whereNotNull')
            ->once()
            ->with('data->key', 'and');
        $filter = new ApplyJsonKey();
        $filter->apply($builder, 'data', 'key', []);
        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_whereNotNull_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('whereNotNull')
            ->once()
            ->with('data->prop', 'or');
        $filter = new ApplyJsonKey();
        $filter->apply($builder, 'data', 'prop', ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

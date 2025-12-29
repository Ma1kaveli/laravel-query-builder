<?php
namespace Tests\Unit\Filters\String;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\String\ApplyJsonContains;
use Tests\TestCase;

class ApplyJsonContainsTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereJsonContains');
        $filter = new ApplyJsonContains();
        $filter->apply($builder, null, 'value', []);
        $this->assertTrue(true);
    }

    /**
     * value не строка → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereJsonContains');
        $filter = new ApplyJsonContains();
        $filter->apply($builder, 'data', 123, []);
        $this->assertTrue(true);
    }

    /**
     * AND логика по умолчанию
     */
    public function test_apply_calls_whereJsonContains_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyJsonContains::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'data')
            ->andReturn('table.data');
        $builder->shouldReceive('whereJsonContains')
            ->once()
            ->with('table.data', 'key', 'and');
        $filter->apply($builder, 'data', 'key', []);
        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_whereJsonContains_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyJsonContains::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'data')
            ->andReturn('table.data');
        $builder->shouldReceive('whereJsonContains')
            ->once()
            ->with('table.data', 'value', 'or');
        $filter->apply($builder, 'data', 'value', ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

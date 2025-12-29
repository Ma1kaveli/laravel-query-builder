<?php

namespace Tests\Unit\Filters\Logic;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Logic\ApplyExistsByBoolean;
use Tests\TestCase;

class ApplyExistsByBooleanTest extends TestCase
{
    /**
     * Если filterableField не строка → ранний return
     */
    public function test_apply_returns_early_if_filterableField_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereNotNull');
        $builder->shouldNotReceive('whereNull');

        $filter = new ApplyExistsByBoolean();
        $filter->apply($builder, true, true, []);
        $this->assertTrue(true);
    }

    /**
     * Если значение null и canBeNull=false → ранний return
     */
    public function test_apply_returns_early_if_value_is_null_and_cannot_be_null()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereNotNull');
        $builder->shouldNotReceive('whereNull');

        $filter = new ApplyExistsByBoolean();
        $filter->apply($builder, null, null, ['filterable_field' => 'field', 'can_be_null' => false]);
        $this->assertTrue(true);
    }

    /**
     * Проверка вызова whereNotNull с AND логикой (value=true)
     */
    public function test_apply_calls_whereNotNull_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyExistsByBoolean::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('whereNotNull')->once()->with('table.field');

        $filter->apply($builder, null, true, ['filterable_field' => 'field']);
        $this->assertTrue(true);
    }

    /**
     * Проверка вызова whereNull с OR логикой (value=false + is_or_where=true)
     */
    public function test_apply_calls_whereNull_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyExistsByBoolean::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('orWhereNull')->once()->with('table.field');

        $filter->apply($builder, null, false, ['filterable_field' => 'field', 'is_or_where' => true]);
        $this->assertTrue(true);
    }

    /**
     * Проверка инверсии
     */
    public function test_apply_inverts_value_correctly()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $filter = Mockery::mock(ApplyExistsByBoolean::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')->once()->with($builder, 'field')->andReturn('table.field');
        $builder->shouldReceive('whereNull')->once()->with('table.field');

        $filter->apply($builder, null, true, ['filterable_field' => 'field', 'invert' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

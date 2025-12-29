<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyArrayDate;
use QueryBuilder\Traits\GetTableField;
use Tests\TestCase;

class ApplyArrayDateTest extends TestCase
{
    /**
     * $field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $this->setDateTimeFormat();
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldNotReceive('whereIn');

        $filter = new ApplyArrayDate();
        $filter->apply($builder, ['field'], ['2025-12-29'], ['is_or_where' => false]);

        $this->assertTrue(true);
    }

    /**
     * $value не массив дат → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_date_array()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldNotReceive('whereIn');

        $filter = new ApplyArrayDate();
        $filter->apply($builder, 'created_at', ['not a date'], ['is_or_where' => false]);

        $this->assertTrue(true);
    }

    /**
     * is_or_where = false → whereIn с 'and'
     */
    public function test_apply_calls_whereIn_with_and()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('whereIn')
            ->once()
            ->with('table.created_at', ['2025-12-29', '2025-12-30'], 'and');

        $filter = Mockery::mock(ApplyArrayDate::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('table.created_at');

        $filter->apply($builder, 'created_at', ['2025-12-29', '2025-12-30'], ['is_or_where' => false]);

        $this->assertTrue(true);
    }

    /**
     * is_or_where = true → whereIn с 'or'
     */
    public function test_apply_calls_whereIn_with_or()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldReceive('whereIn')
            ->once()
            ->with('table.updated_at', ['2025-01-01'], 'or');

        $filter = Mockery::mock(ApplyArrayDate::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'updated_at')
            ->andReturn('table.updated_at');

        $filter->apply($builder, 'updated_at', ['2025-01-01'], ['is_or_where' => true]);

        $this->assertTrue(true);
    }

    /**
     * Проверка пустого массива дат
     * → метод пропускает
     */
    public function test_apply_with_empty_array_returns_early()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldNotReceive('whereIn');

        $filter = new ApplyArrayDate();
        $filter->apply($builder, 'created_at', [], ['is_or_where' => false]);

        $this->assertTrue(true);
    }

    /**
     * Проверка массива с неправильным форматом даты (например, '2025-13-01')
     */
    public function test_apply_with_invalid_date_format_returns_early()
    {
        $this->setDateTimeFormat();
        
        $builder = Mockery::mock(EloquentBuilder::class);

        $builder->shouldNotReceive('whereIn');

        $filter = new ApplyArrayDate();
        $filter->apply($builder, 'created_at', ['2025-13-01'], ['is_or_where' => false]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

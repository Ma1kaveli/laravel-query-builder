<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyTime;
use Tests\TestCase;

class ApplyTimeTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyTime();
        $filter->apply($builder, null, '12:00', []);

        $this->assertTrue(true);
    }

    /**
     * value не валидный формат времени → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_time_format()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyTime();
        $filter->apply($builder, 'created_at', '25:00', []);

        $this->assertTrue(true);
    }

    /**
     * AND логика по умолчанию
     */
    public function test_apply_calls_whereIn_with_and_logic()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyTime::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('table.created_at');

        $builder->shouldReceive('where')
            ->once()
            ->with('table.created_at', '12:00', 'and');

        $filter->apply($builder, 'created_at', '12:00', []);
        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_whereIn_with_or_logic()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyTime::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'updated_at')
            ->andReturn('table.updated_at');

        $builder->shouldReceive('where')
            ->once()
            ->with('table.updated_at', '15:30', 'or');

        $filter->apply($builder, 'updated_at', '15:30', ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

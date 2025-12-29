<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyToday;
use Tests\TestCase;

class ApplyTodayTest extends TestCase
{
    /**
     * Поле не является строкой
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereToday');

        $filter = new ApplyToday();
        $filter->apply($builder, null, null, []);
        $this->assertTrue(true);
    }

    /**
     * AND логика
     */
    public function test_apply_calls_whereToday_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyToday::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('table.created_at');

        $builder->shouldReceive('whereToday')
            ->once()
            ->with('table.created_at', 'and');

        $filter->apply($builder, 'created_at', null, []);
        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_whereToday_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyToday::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'updated_at')
            ->andReturn('table.updated_at');

        $builder->shouldReceive('whereToday')
            ->once()
            ->with('table.updated_at', 'or');

        $filter->apply($builder, 'updated_at', null, ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

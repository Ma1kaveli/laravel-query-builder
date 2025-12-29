<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyLastWeek;
use Tests\TestCase;
use Carbon\Carbon;

class ApplyLastWeekTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereBetween');

        $filter = new ApplyLastWeek();
        $filter->apply(
            $builder,
            null,
            null,
            []
        );

        $this->assertTrue(true);
    }

    /**
     * AND логика по умолчанию
     */
    public function test_apply_calls_whereBetween_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyLastWeek::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('table.created_at');

        $builder->shouldReceive('whereBetween')
            ->once()
            ->with(
                'table.created_at',
                Mockery::on(function ($dates) {
                    return count($dates) === 2
                        && $dates[0] instanceof Carbon
                        && $dates[1] instanceof Carbon;
                }),
                'and'
            );

        $filter->apply(
            $builder,
            'created_at',
            null,
            []
        );

        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_whereBetween_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyLastWeek::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'updated_at')
            ->andReturn('table.updated_at');

        $builder->shouldReceive('whereBetween')
            ->once()
            ->with(
                'table.updated_at',
                Mockery::on(function ($dates) {
                    return count($dates) === 2
                        && $dates[0] instanceof Carbon
                        && $dates[1] instanceof Carbon;
                }),
                'or'
            );

        $filter->apply(
            $builder,
            'updated_at',
            null,
            ['is_or_where' => true]
        );

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

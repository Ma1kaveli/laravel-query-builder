<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyTimeRange;
use Tests\TestCase;

class ApplyTimeRangeTest extends TestCase
{
    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $this->setDateTimeFormat();
        
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyTimeRange();
        $filter->apply($builder, null, ['12:00', '14:00'], []);

        $this->assertTrue(true);
    }

    /**
     * value невалидный диапазон → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_time_range()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyTimeRange();
        $filter->apply($builder, 'created_at', ['25:00', '26:00'], []);

        $this->assertTrue(true);
    }

    /**
     * AND логика
     */
    public function test_apply_calls_where_with_and_logic()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyTimeRange::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('table.created_at');

        $builder->shouldReceive('where')
            ->once()
            ->withArgs(function ($closure, $a, $b, $logic) {
                $mockQuery = Mockery::mock(EloquentBuilder::class);
                $mockQuery->shouldReceive('whereTime')
                    ->once()
                    ->with('table.created_at', '>=', '12:00')
                    ->andReturnSelf();
                $mockQuery->shouldReceive('whereTime')
                    ->once()
                    ->with('table.created_at', '<=', '14:00')
                    ->andReturnSelf();

                $closure($mockQuery);
                return $logic === 'and';
            });

        $filter->apply($builder, 'created_at', ['12:00', '14:00'], []);
        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_where_with_or_logic()
    {
        $this->setDateTimeFormat();

        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyTimeRange::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'updated_at')
            ->andReturn('table.updated_at');

        $builder->shouldReceive('where')
            ->once()
            ->withArgs(function ($closure, $a, $b, $logic) {
                $mockQuery = Mockery::mock(EloquentBuilder::class);
                $mockQuery->shouldReceive('whereTime')
                    ->once()
                    ->with('table.updated_at', '>=', '10:00')
                    ->andReturnSelf();
                $mockQuery->shouldReceive('whereTime')
                    ->once()
                    ->with('table.updated_at', '<=', '18:00')
                    ->andReturnSelf();

                $closure($mockQuery);
                return $logic === 'or';
            });

        $filter->apply($builder, 'updated_at', ['10:00', '18:00'], ['is_or_where' => true]);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

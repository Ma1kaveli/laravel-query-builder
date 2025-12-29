<?php

namespace Tests\Unit\Filters\Datetime;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Datetime\ApplyDay;
use Tests\TestCase;

class ApplyDayTest extends TestCase
{
    /**
     * value не день месяца → ранний return
     */
    public function test_apply_returns_early_if_value_is_not_day()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereDay');

        $filter = new ApplyDay();
        $filter->apply(
            $builder,
            'created_at',
            50, // некорректный день
            []
        );

        $this->assertTrue(true);
    }

    /**
     * AND логика по умолчанию
     */
    public function test_apply_calls_where_day_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyDay::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'created_at')
            ->andReturn('users.created_at');

        $builder->shouldReceive('whereDay')
            ->once()
            ->with(
                'users.created_at',
                15,
                null,
                'and'
            );

        $filter->apply(
            $builder,
            'created_at',
            15,
            []
        );

        $this->assertTrue(true);
    }

    /**
     * OR логика
     */
    public function test_apply_calls_where_day_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyDay::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->once()
            ->with($builder, 'birthday')
            ->andReturn('profiles.birthday');

        $builder->shouldReceive('whereDay')
            ->once()
            ->with(
                'profiles.birthday',
                1,
                null,
                'or'
            );

        $filter->apply(
            $builder,
            'birthday',
            1,
            [
                'is_or_where' => true
            ]
        );

        $this->assertTrue(true);
    }

    /**
     * field не строка → ранний return
     */
    public function test_apply_returns_early_if_field_is_not_string()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereDay');

        $filter = new ApplyDay();
        $filter->apply(
            $builder,
            null,
            10,
            []
        );

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

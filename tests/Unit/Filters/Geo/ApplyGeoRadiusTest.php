<?php

namespace Tests\Unit\Filters\Geo;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Geo\ApplyGeoRadius;
use Tests\TestCase;

class ApplyGeoRadiusTest extends TestCase
{
    /**
     * Если значения координат и радиус не строки → ранний return
     */
    public function test_apply_returns_early_if_coordinates_or_radius_are_not_strings()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereRaw');

        $filter = new ApplyGeoRadius();
        $filter->apply($builder, null, null, [
            'lon_field' => 'lon',
            'lat_field' => 'lat',
            'lon' => 10,
            'lat' => 20,
            'radius' => 1000
        ]);

        $this->assertTrue(true);
    }

    /**
     * Проверка правильного вызова whereRaw с AND логикой
     */
    public function test_apply_calls_whereRaw_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyGeoRadius::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->with($builder, 'lon')
            ->andReturn('table.lon');
        $filter->shouldReceive('getFieldWithTable')
            ->with($builder, 'lat')
            ->andReturn('table.lat');

        $builder->shouldReceive('whereRaw')
            ->once()
            ->withArgs(function ($sql, $bindings, $logic) {
                return str_contains($sql, 'ST_Distance_Sphere')
                    && $logic === 'and'
                    && count($bindings) === 3;
            });

        $filter->apply($builder, null, null, [
            'lon_field' => 'lon',
            'lat_field' => 'lat',
            'lon' => '10',
            'lat' => '20',
            'radius' => '1000'
        ]);

        $this->assertTrue(true);
    }

    /**
     * Проверка OR логики
     */
    public function test_apply_calls_whereRaw_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyGeoRadius::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $filter->shouldReceive('getFieldWithTable')
            ->with($builder, 'lon')
            ->andReturn('table.lon');
        $filter->shouldReceive('getFieldWithTable')
            ->with($builder, 'lat')
            ->andReturn('table.lat');

        $builder->shouldReceive('whereRaw')
            ->once()
            ->withArgs(fn($sql, $bindings, $logic) => $logic === 'or');

        $filter->apply($builder, null, null, [
            'lon_field' => 'lon',
            'lat_field' => 'lat',
            'lon' => '10',
            'lat' => '20',
            'radius' => '1000',
            'is_or_where' => true,
        ]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

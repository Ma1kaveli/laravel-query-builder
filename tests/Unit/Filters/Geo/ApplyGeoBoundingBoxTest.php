<?php

namespace Tests\Unit\Filters\Geo;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Geo\ApplyGeoBoundingBox;
use Tests\TestCase;

class ApplyGeoBoundingBoxTest extends TestCase
{
    /**
     * Если значения координат не строки → ранний return
     */
    public function test_apply_returns_early_if_coordinates_are_not_strings()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('whereRaw');

        $filter = new ApplyGeoBoundingBox();
        $filter->apply($builder, null, null, [
            'lon_field' => 'lon',
            'lat_field' => 'lat',
            'min_lon' => 10,
            'min_lat' => 20,
            'max_lon' => 30,
            'max_lat' => 40
        ]);

        $this->assertTrue(true);
    }

    /**
     * Проверка правильного вызова whereRaw с AND логикой
     */
    public function test_apply_calls_whereRaw_with_and_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyGeoBoundingBox::class)
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
                return str_contains($sql, 'MBRContains')
                    && $logic === 'and'
                    && count($bindings) === 1;
            });

        $filter->apply($builder, null, null, [
            'lon_field' => 'lon',
            'lat_field' => 'lat',
            'min_lon' => '10',
            'min_lat' => '20',
            'max_lon' => '30',
            'max_lat' => '40',
        ]);

        $this->assertTrue(true);
    }

    /**
     * Проверка OR логики
     */
    public function test_apply_calls_whereRaw_with_or_logic()
    {
        $builder = Mockery::mock(EloquentBuilder::class);

        $filter = Mockery::mock(ApplyGeoBoundingBox::class)
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
                return $logic === 'or';
            });

        $filter->apply($builder, null, null, [
            'lon_field' => 'lon',
            'lat_field' => 'lat',
            'min_lon' => '10',
            'min_lat' => '20',
            'max_lon' => '30',
            'max_lat' => '40',
            'is_or_where' => true,
        ]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

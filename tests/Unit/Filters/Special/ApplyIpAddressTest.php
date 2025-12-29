<?php

namespace Tests\Unit\Filters\Special;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Special\ApplyIpAddress;
use Tests\TestCase;

class ApplyIpAddressTest extends TestCase
{
    /**
     * Ранний return если ip, cidr или field не строка
     */
    public function test_apply_returns_early_if_ip_cidr_or_field_invalid()
    {
        $modelMock = Mockery::mock();
        $modelMock->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($modelMock);
        $builder->shouldNotReceive('whereRaw');

        $filter = new ApplyIpAddress();
        $filter->apply($builder, null, null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка whereRaw
     */
    public function test_apply_calls_whereRaw()
    {
        $modelMock = Mockery::mock();
        $modelMock->shouldReceive('getTable')->andReturn('table');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($modelMock);
        $builder->shouldReceive('whereRaw')->once()->with(
            Mockery::type('string'),
            ['127.0.0.1', 24],
            'and'
        );

        $filter = new ApplyIpAddress();
        $filter->apply($builder, 'field', null, ['ip' => '127.0.0.1', 'cidr' => '24']);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

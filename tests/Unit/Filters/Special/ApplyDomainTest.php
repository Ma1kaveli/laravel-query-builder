<?php

namespace Tests\Unit\Filters\Special;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Mockery;
use QueryBuilder\Filters\Special\ApplyDomain;
use Tests\TestCase;

class ApplyDomainTest extends TestCase
{
    /**
     * Ранний return если поле или значение не строка/массив строк
     */
    public function test_apply_returns_early_if_field_or_value_invalid()
    {
        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldNotReceive('where');

        $filter = new ApplyDomain();
        $filter->apply($builder, null, null, []);
        $this->assertTrue(true);
    }

    /**
     * Проверка where с одиночным значением
     */
    public function test_apply_calls_where_with_single_value()
    {
        $modelMock = Mockery::mock();
        $modelMock->shouldReceive('getTable')->andReturn('users');

        $queryMock = Mockery::mock(EloquentBuilder::class);
        $queryMock->shouldReceive('getModel')->andReturn($modelMock);
        $queryMock->shouldReceive('where')->once();

        $filter = new ApplyDomain();
        $filter->apply($queryMock, 'email', 'example.com');
        $this->assertTrue(true);
    }


    /**
     * Проверка where с массивом значений
     */
    public function test_apply_calls_where_with_array_value()
    {
        $modelMock = Mockery::mock();
        $modelMock->shouldReceive('getTable')->andReturn('table');

        $queryMock = Mockery::mock(EloquentBuilder::class);
        $queryMock->shouldReceive('getModel')->andReturn($modelMock);

        // проверяем where с замыканием
        $queryMock->shouldReceive('where')
            ->once()
            ->with(
                Mockery::on(fn($arg) => $arg instanceof Closure),
                null,
                null,
                'and'
            );

        $filter = new ApplyDomain();
        $filter->apply($queryMock, 'field', ['example.com', 'example.org']);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

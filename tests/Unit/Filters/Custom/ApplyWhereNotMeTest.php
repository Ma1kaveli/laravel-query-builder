<?php

namespace Tests\Unit\Filters\Custom;

use Tests\TestCase;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Auth;
use QueryBuilder\Filters\Custom\ApplyWhereNotMe;
use Mockery;
use Tests\Fakes\FakeUser;

class ApplyWhereNotMeTest extends TestCase
{
    /**
     * Функция должна добавить where-условие
     */
    public function test_apply_adds_where_with_authenticated_user()
    {
        $user = new FakeUser(['id' => 1]);
        Auth::setUser($user);

        $model = Mockery::mock();
        $model->shouldReceive('getTable')->andReturn('users');

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('getModel')->andReturn($model);
        $builder->shouldReceive('where')
            ->once()
            ->withArgs(function ($field, $operator, $value) use ($user) {
                return $field === 'users.id' && $operator === '!=' && $value === $user->id;
            });

        $filter = new ApplyWhereNotMe();
        $filter->apply($builder, 'id', true);

        $this->assertTrue(true);
    }

    /**
     * Функция должна ничего не делать, если пользователь не аутентифицирован
     */
    public function test_apply_does_nothing_when_no_user()
    {
        Auth::shouldReceive('user')->andReturn(null);

        $builder = Mockery::mock(EloquentBuilder::class);
        $builder->shouldReceive('where')->never();

        $filter = new ApplyWhereNotMe();
        $filter->apply($builder, 'user_id', true);
        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
        Mockery::close();
        Auth::swap(null);
    }
}

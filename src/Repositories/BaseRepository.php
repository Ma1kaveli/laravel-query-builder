<?php

namespace QueryBuilder\Repositories;

use QueryBuilder\DTO\ListDTO;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\{Builder, Model, SoftDeletes};
use Illuminate\Support\Facades\Auth;

class BaseRepository
{
    /**
     * Model::class
     */
    protected Model $model;

    public ?Authenticatable $user;

    public function __construct(Model $model, ?Authenticatable $user = null)
    {
        $this->model = $model;
        $this->user = $user ?? Auth::user();
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->model::all();
    }

    /**
     * @return array|Collection
     */
    public function getAll(): array|Collection
    {
        return $this->model::get();
    }

    /**
     * @param int $id
     * @param bool $withTrashed
     * @param string $notFoundMessage = 'Не найдено!'
     *
     * @return array|Collection|Model|array<Model>
     */
    public function findByIdOrFail(
        int $id,
        bool $withTrashed = false,
        string $notFoundMessage = 'Не найдено!',
    ): array|Collection|Model {
        try {
            $query = $this->model::when(
                $withTrashed, fn ($q) => $q->withTrashed()
            )->findOrFail($id);
        } catch(\Exception $e) {
            throw new \Exception($notFoundMessage, 404);
        }

        return $query;
    }

    /**
     * isUnique
     *
     * @param mixed $dto
     * @param array $mapParams - example [
     *      'name' => [
     *           'column' => \DB::raw('LOWER(name)'),
     *           'modifier' => fn($v) => trim(strtolower($v)),
     *           'is_or_where' => false
     *       ],
     *       'organizationId' => 'organization_id',
     *   ]
     * @param bool $exceptIfExist = true
     * @param string $exceptMessage = 'Нельзя дублировать записи'
     * @param string $excludeKey = 'id'
     * @param string $excludeColumn = 'id'
     *
     * @return bool|\Exception
     */
    public function isUnique(
        mixed $dto,
        array $mapParams,
        bool $exceptIfExist = true,
        string $exceptMessage = 'Нельзя дублировать записи',
        string $excludeKey = 'id',
        string $excludeColumn = 'id'
    ): bool|\Exception {
        $query = $this->model;

        if (isset($dto->{$excludeKey}) && !empty($dto->{$excludeKey})) {
            $query->where($excludeColumn, '!=', $dto->{$excludeKey});
        }

        foreach ($mapParams as $dtoProperty => $mapping) {
            if (!isset($dto->{$dtoProperty})) {
                continue;
            }

            $value = $dto->{$dtoProperty};

            $column = is_array($mapping) ? ($mapping['column'] ?? null) : $mapping;
            $modifier = is_array($mapping) ? ($mapping['modifier'] ?? null) : null;
            $isOrWhere = is_array($mapping) ? ($mapping['is_or_where'] ?? false) : false;

            if ($modifier && is_callable($modifier)) {
                $value = $modifier($value);
            }

            $query->where($column, $value, null, $isOrWhere ? 'or' : 'and');
        }

        $isUnique = !$query->exists();

        if ($exceptIfExist && $isUnique) {
            throw new \Exception($exceptMessage, 404);
        }

        return $isUnique;
    }

    /**
     * @param string $column
     * @param $value
     *
     * @return Model|object|null
     */
    public function findBy(string $column, $value)
    {
        return $this->model::where($column, $value)->first();
    }

    /**
     * @param string $column
     * @param $value
     *
     * @return array|Collection
     */
    public function getBy(string $column, $value): array|Collection
    {
        return $this->model::where($column, $value)->get();
    }

    /**
     *
     * @param array $data
     *
     * @return Builder|Model|object|null
     */
    public function findByColumns(array $data)
    {
        $query = $this->model::query();
        foreach ($data as $key => $value) {
            $query = $query->where($key, $value);
        }

        return $query->first();
    }

    /**
     * findById
     *
     * @param int $id
     *
     * @return array|Collection|Model|null
     */
    public function findById(mixed $id): array|Collection|Model|null
    {
        return $this->model::find($id);
    }

    /**
     * @return bool
     */
    public function isAuth(): bool {
        if (!empty($this->user)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        $isSuperadminField = config('query-builder.repository.is_root_field', 'is_superadministrator');
        return $this->isAuth() && !!$this->user->{$isSuperadminField};
    }

    /**
     * @return int|null
     */
    public function getAuthUserId(): int|null {
        $userIdField = config('query-builder.repository.user_id_field', 'id');
        return $this->isAuth() ? $this->user->{$userIdField} : null;
    }

    /**
     * @param bool $withTrashed = false
     *
     * @return Builder
     */
    public function appendWithTrashedToQuery(bool $withTrashed = false): Builder {
        $query = $this->model->query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query;
    }

    /**
     * @param bool $withTrashed = false
     *
     * @return int
     */
    public function count(bool $withTrashed = false): int|null {
        $query = $this->appendWithTrashedToQuery($withTrashed);

        return $query->count();
    }

    /**
     * @param string $column = 'created_at'
     * @param bool $withTrashed = false
     *
     * @return int|null
     */
    public function countToday(string $column = 'created_at', bool $withTrashed = false): int|null {
        $query = $this->appendWithTrashedToQuery($withTrashed);

        return $query->whereDate($column, Carbon::today())->count();
    }

    /**
     * @param string $column = 'created_at'
     * @param bool $withTrashed = false
     *
     * @return int|null
     */
    public function countThisWeek(string $column = 'created_at', bool $withTrashed = false): int|null {
        $query = $this->appendWithTrashedToQuery($withTrashed);

        return $query->whereBetween(
            $column,
            [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
        )->count();
    }

    /**
     * @param string $column = 'created_at'
     * @param bool $withTrashed = false
     *
     * @return int|null
     */
    public function countThisMonth(string $column = 'created_at', bool $withTrashed = false): int|null {
        $query = $this->appendWithTrashedToQuery($withTrashed);

        return $query->whereMonth(
            $column, Carbon::now()->month
        )->whereYear($column, Carbon::now()->year)->count();
    }

    /**
     * @param string $column = 'created_at'
     * @param bool $withTrashed = false
     *
     * @return int|null
     */
    public function countThisYear(string $column = 'created_at', bool $withTrashed = false): int|null {
        $query = $this->appendWithTrashedToQuery($withTrashed);

        return $query->whereYear($column, Carbon::now()->year)->count();
    }

    /**
     * @param string $column
     * @param bool $withTrashed = false
     *
     * @return int|null
     */
    public function sum(string $column, bool $withTrashed = false): int|null {
        $query = $this->appendWithTrashedToQuery($withTrashed);

        return $query->sum($column);
    }

    /**
     * @param string $column
     * @param string $dateColumn = 'created_at'
     * @param bool $withTrashed = false
     *
     * @return int|null
     */
    public function sumToday(string $column, string $dateColumn = 'created_at', bool $withTrashed = false): int|null {
        $query = $this->appendWithTrashedToQuery($withTrashed);

        return $query->whereDate($dateColumn, Carbon::today())->sum($column);
    }

    /**
     * @param string $column
     * @param string $dateColumn = 'created_at'
     * @param bool $withTrashed = false
     *
     * @return int|null
     */
    public function sumThisWeek(string $column, string $dateColumn = 'created_at', bool $withTrashed = false): int|null {
        $query = $this->appendWithTrashedToQuery($withTrashed);

        return $query->whereBetween(
            $dateColumn,
            [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
        )->sum($column);
    }

    /**
     * @param string $column
     * @param string $dateColumn = 'created_at'
     * @param bool $withTrashed = false
     *
     * @return int
     */
    public function sumThisMonth(string $column, string $dateColumn = 'created_at', bool $withTrashed = false): int|null {
        $query = $this->appendWithTrashedToQuery($withTrashed);

        return $query->whereMonth(
            $dateColumn, Carbon::now()->month
        )->whereYear($dateColumn, Carbon::now()->year)->sum($column);
    }

    /**
     * @param string $column
     * @param string $dateColumn = 'created_at'
     * @param bool $withTrashed = false
     *
     * @return int
     */
    public function sumThisYear(string $column, string $dateColumn = 'created_at', bool $withTrashed = false): int|null {
        $query = $this->appendWithTrashedToQuery($withTrashed);

        return $query->whereYear($dateColumn, Carbon::now()->year)->sum($column);
    }

    /**
     * @param string $column = 'created_at'
     * @param int $count = 5
     * @param bool $withTrashed = false
     *
     * @return mixed
     */
    public function getLatest(string $column = 'id', int $count = 5, bool $withTrashed = false): mixed {
        $query = $this->appendWithTrashedToQuery($withTrashed);

        return $query->orderBy($column, 'desc')->take($count)->get();
    }

    /**
     * @param string $column
     * @param bool $withTrashed = false
     *
     * @return float|int|null
     */
    public function avg(string $column, bool $withTrashed = false): float|int|null {
        $query = $this->appendWithTrashedToQuery($withTrashed);

        return $query->avg($column);
    }

    /**
     * getPaginatedList
     *
     * @param ListDTO|mixed $dto
     *
     * @return LengthAwarePaginator
     */
    public function getPaginatedList(mixed $dto): LengthAwarePaginator
    {
        return $this->model->filter($dto->params)->list();
    }
}

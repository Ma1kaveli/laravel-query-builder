# makaveli/laravel-query-builder  
**Мощный и удобный конструктор фильтров для Laravel Eloquent**

## Описание

Библиотека предоставляет удобный способ создания сложных, читаемых и расширяемых фильтров для Eloquent-запросов в Laravel.

Основная идея — вынести всю логику фильтрации, сортировки и пагинации в отдельные классы-наследники от `BaseQueryBuilder`. Каждый метод такого класса отвечает за применение одного типа условия.

Это позволяет:
- держать контроллеры и репозитории чистыми
- легко добавлять/изменять фильтры
- переиспользовать фильтры между разными модулями
- централизованно контролировать безопасность параметров
- получать очень читаемый код фильтрации

## Установка

```bash
composer require makaveli/laravel-query-builder
```

(Опционально) Опубликовать конфиг, если хотите поменять дефолтные настройки:

```bash
php artisan vendor:publish --tag=query-builder-config
```

## Быстрый старт — реальный пример использования

```php
<?php

namespace App\Modules\Organization\Filters;

use App\Modules\Organization\Constants\OrganizationTypeSlugs;
use Illuminate\Pagination\LengthAwarePaginator;
use QueryBuilder\BaseQueryBuilder;
use QueryBuilder\DTO\AvailableSort;
use QueryBuilder\DTO\AvailableSorts;

class OrganizationFilters extends BaseQueryBuilder
{
    /**
     * Основной метод для списка организаций в админке
     */
    public function list(): LengthAwarePaginator
    {
        // Поиск по множеству полей одной строкой
        $this->applyLike([
            'name', 'short_name', 'name_en',
            'inn', 'ogrn', 'phone',
            'email'
        ], 'search');

        // Блокируем показ "корневой" организации (администрации сайта)
        $this->query->whereHas(
            'organizationType',
            fn ($q) => $q->where('slug', '!=', OrganizationTypeSlugs::ROOT_SLUG)
        );

        // Фильтры
        $this->applyInteger('organization_type_id');
        $this->applyWhereHasLike('organizationType', 'slug', 'organization_type_slug');

        // Показывать удалённые записи, если попросили
        $this->applyWithDeleted();

        // Сортировки
        $this->sortBy(['name', 'inn', 'phone', 'ogrn']);

        // Сортировка по полю из связанной таблицы (organization_types.name)
        $this->sortByRelationField(
            new AvailableSorts([
                new AvailableSort(
                    'organizations.organization_types.name',     // ключ в запросе (как будет приходить в sort_by)
                    'organization_types',                        // таблица связи
                    'id',                                        // внешний ключ в organization_types
                    'organization_type_id'                       // ключ в основной таблице organizations
                ),
            ]),
            'organizations'                                      // имя основной таблицы
        );

        // Пагинация с поддержкой ?rows_per_page=all (если разрешено)
        return $this->applyPaginate(
            canAllRows: true,
            maxRowsPerPage: 150
        );
    }

    /**
     * Метод для публичного каталога (немного проще)
     */
    public function getCatalogPaginatedList(): LengthAwarePaginator
    {
        $this->applyLike([
            'name', 'short_name', 'name_en',
            'inn', 'ogrn', 'phone',
            'email'
        ], 'search');

        // Загружаем тип организации
        $this->with('organizationType');

        $this->applyWhereHasLike('organizationType', 'slug', 'organization_type_slug');

        $this->sortBy(['name', 'inn', 'phone', 'ogrn']);

        return $this->applyPaginate();
    }
}
```

Подробности каждого метода вы можете посмотреть в папке docs:
- **[Docs](./docs/BaseQueryBuilder.md)**

## Основная архитектура

```
QueryBuilder
├── BaseQueryBuilder.php          ← абстрактный базовый класс (наследуете его)
├── DTO                           ← DTO-структуры для сложных параметров
│   ├── AvailableSort.php
│   ├── AvailableSorts.php
│   ├── DeepWhereHasWhereParam.php
│   └── DeepWhereHasWhereParams.php
├── Filters                       ← все конкретные фильтры разделены по категориям
│   ├── Combine                   ← сложные комбинированные фильтры
│   ├── Custom
│   ├── Datetime                  ← все что связано с датами/временем
│   ├── Geo
│   ├── Logic                     ← null, boolean, empty, true/false
│   ├── Numeric
│   ├── Relation
│   ├── Special                   ← специфичные бизнес-фильтры
│   ├── String
│   └── System                    ← limit, select, distinct, with, withCount...
└── Builders / Factories          ← вспомогательные классы для разных СУБД
```

## Основные возможности

Категория                | Кол-во фильтров | Примеры методов / возможностей
------------------------|------------------|--------------------------------
Даты и время            | ~20             | `applyToday()`, `applyLastWeek()`, `applyDateRange()`, `applyCurrentHour()`, `applyTimeStartEnd()`, `applyLastMonth()`...
Числа                   | ~15             | `applyNumericRange()`, `applyMultipleOf()`, `applyEvenNumeric()`, `applyArrayInteger()`...
Строки                  | ~8–10           | `applyLike()`, `applyLikeStart()`, `applyLikeEnd()`, `applyRegex()`, `applyDomain()`
JSON                    | 2–3             | `applyJsonContains()`, `applyJsonKey()`
Отношения (whereHas)    | ~10+            | `applyDeepWhereHasWhere()`, `applyCrossUponCrossWhereHasWhere()`, `applyWhereHasLikeArray()`
Логика / null / boolean | ~8              | `applyNull()`, `applyTrue()`, `applyFalse()`, `applyNotNull()`, `applyEmpty()`
Системные               | ~10             | `applyLimit()`, `applySelect()`, `applyDistinct()`, `with()`, `withCount()`, `inRandomOrder()`
Сортировки              | 2 основных      | `sortBy()`, `sortByRelationField()` + поддержка DTO `AvailableSorts`
Пагинация               | 1               | `applyPaginate()` — с поддержкой all-rows, max limit, кастомными именами параметров
Специальные / бизнес    | 4+              | `applyIpAddress()`, `applyRating()`, `applyStock()`
Геолокация              | 2               | `applyGeoRadius()`, `applyGeoBoundingBox()`

## Как использовать в репозитории

```php
use App\Modules\Organization\Filters\OrganizationFilters;
use App\Modules\Organization\DTO\ListDTO; // или ваш собственный DTO

public function getList(ListDTO $dto): LengthAwarePaginator
{
    $filters = new OrganizationFilters(
        (new Organization())->query(),
        $dto->toArray() // или $dto->params — как у вас реализовано
    );

    return $filters->list(); // или ->getCatalogPaginatedList() в зависимости от контекста
}
```

## Основные преимущества подхода

- **Читаемость** — каждый `$this->apply...()` — одно понятное условие
- **Безопасность** — параметры берутся только из `$this->params`
- **Расширяемость** — легко добавить новый фильтр в `Filters/`
- **Переиспользование** — один и тот же класс фильтров можно использовать в разных контекстах (админка, каталог, API, отчёты)
- **Типизация** — все методы строго типизированы

## Полезные ссылки

- Репозиторий библиотеки: https://github.com/Ma1kaveli/laravel-query-builder  
- Базовый набор утилит и DTO (очень рекомендуется): https://github.com/Ma1kaveli/laravel-core  
- Полный список доступных фильтров: смотрите папку `src/Filters` в репозитории

Удачи с проектами и быстрых фильтров! 🚀

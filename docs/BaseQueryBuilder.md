# Документация для класса BaseQueryBuilder

## Обзор

`BaseQueryBuilder` — абстрактный класс в пространстве имён `QueryBuilder`, предназначенный для построения запросов на основе Laravel Eloquent Query Builder. Он предоставляет множество защищённых методов для применения фильтров, сортировок, пагинации и других операций над запросами. Класс работает с параметрами из массива `$params` и модифицирует объект `$query`.

Класс использует различные фильтры из подпапок `Filters` для реализации логики. Методы часто рекурсивно применяются к массивам полей и поддерживают логику OR/AND.

Расширьте этот класс для конкретных моделей и вызывайте методы в методе `build()` или аналогичном.

Пример использования:
```php
class UserQueryBuilder extends BaseQueryBuilder
{
    public function list(): EloquentQueryBuilder
    {
        $this->applyLike('name', 'search');
        $this->applyDateRange('created_at', 'date_range');
        $this->sortBy(['id', 'name']);
        $this->applyPaginate();
        return $this->query;
    }
}
```

## Свойства

- `protected array $params`: Массив входных параметров для фильтров.
- `protected EloquentQueryBuilder|QueryBuilder|LengthAwarePaginator $query`: Объект запроса.

## Конструктор

| Метод          | Параметры                                      | Описание |
|----------------|------------------------------------------------|----------|
| `__construct`  | EloquentQueryBuilder\|QueryBuilder $query, array $params = [] | Инициализирует builder с запросом и параметрами. |

## Методы получения результатов

| Метод    | Параметры                        | Описание |
|----------|----------------------------------|----------|
| `get`    | array\|string $columns = ['*']   | Получает коллекцию результатов. |
| `all`    | array\|string $columns = ['*']   | Получает все результаты в массиве. |
| `first`  | array\|string $columns = ['*']   | Получает первый результат. |
| `latest` | array\|string $columns = ['*']   | Получает последний результат (по умолчанию по created_at). |

## Пагинация и ограничения

| Метод           | Параметры                                                                 | Описание |
|-----------------|---------------------------------------------------------------------------|----------|
| `applyPaginate` | bool $canAllRows = false, array\|string $columns = ['*'], string $rowsPerPageName = 'rows_per_page', string $pageName = 'page', int $maxRowsPerPage = 100 | Применяет пагинацию с опцией получения всех строк. |
| `applyLimit`    | string $paramName = 'limit'                                               | Устанавливает лимит результатов. |

## Загрузка отношений

| Метод        | Параметры             | Описание |
|--------------|-----------------------|----------|
| `with`       | array\|string $relationships | Загружает отношения. |
| `withCount`  | array\|string $relationships | Загружает количество связанных записей. |

## Сортировка и порядок

| Метод                  | Параметры                                                                 | Описание |
|------------------------|---------------------------------------------------------------------------|----------|
| `sortBy`               | array $availableSorts, ?string $defaultField = 'id', string $paramName = 'sort_by', string $directionParamName = 'descending' | Применяет сортировку по доступным полям. |
| `sortByRelationField`  | AvailableSorts $availableSorts, string $ownerTable, string $paramName = 'sort_by', string $directionParamName = 'descending', array $columns = ['*'] | Сортировка по полю связанной таблицы. |
| `inRandomOrder`        | -                                                                         | Результаты в случайном порядке. |

## Soft Deletes и архивация

| Метод               | Параметры                          | Описание |
|---------------------|------------------------------------|----------|
| `applyWithDeleted`  | string $paramName = 'show_deleted' | Включает удалённые записи. |
| `applyOnlyDeleted`  | string $paramName = 'only_deleted' | Только удалённые записи. |
| `applyArchived`     | string $field                      | Фильтр по архивным записям. |

## Выборка и уникальность

| Метод            | Параметры              | Описание |
|------------------|------------------------|----------|
| `applySelect`    | array $columns = ['*'] | Выбирает столбцы. |
| `applyDistinct`  | -                      | Уникальные результаты. |

## Фильтры по датам и времени

| Метод                  | Параметры                                                       | Описание |
|------------------------|-----------------------------------------------------------------|----------|
| `applyArrayDate`       | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по массиву дат. |
| `applyArrayTime`       | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по массиву времён. |
| `applyCurrentHour`     | array\|string $field, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по текущему часу. |
| `applyCurrentMinute`   | array\|string $field, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по текущей минуте. |
| `applyDate`            | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по дате. |
| `applyDateEnd`         | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по концу даты. |
| `applyDateNotRange`    | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр вне диапазона дат. |
| `applyDateRange`       | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по диапазону дат. |
| `applyDateStart`       | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по началу даты. |
| `applyDateStartEnd`    | array\|string $field, string $paramStartName, string $paramEndName, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по диапазону дат (начало/конец). |
| `applyDay`             | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по дню. |
| `applyHour`            | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по часу. |
| `applyLastMonth`       | array\|string $field, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по прошлому месяцу. |
| `applyLastWeek`        | array\|string $field, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по прошлой неделе. |
| `applyLastYear`        | array\|string $field, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по прошлому году. |
| `applyMinute`          | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по минуте. |
| `applyMonth`           | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по месяцу. |
| `applyTime`            | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по времени. |
| `applyTimeEnd`         | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по концу времени. |
| `applyTimeRange`       | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по диапазону времени. |
| `applyTimeStart`       | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по началу времени. |
| `applyTimeStartEnd`    | array\|string $field, string $paramStartName, string $paramEndName, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по диапазону времени (начало/конец). |
| `applyToday`           | array\|string $field, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по сегодняшнему дню. |
| `applyYear`            | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по году. |

## Фильтры по числам

| Метод                     | Параметры                                                       | Описание |
|---------------------------|-----------------------------------------------------------------|----------|
| `applyArrayDouble`        | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по массиву double. |
| `applyArrayFloat`         | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по массиву float. |
| `applyArrayInteger`       | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по массиву integer. |
| `applyArrayNumeric`       | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по массиву чисел. |
| `applyDouble`             | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по double. |
| `applyEvenNumeric`        | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по чётным числам. |
| `applyFloat`              | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по float. |
| `applyInteger`            | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по integer. |
| `applyMultipleOf`         | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по кратным числам. |
| `applyNumeric`            | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по числу. |
| `applyNumericGreaterThan` | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр > числа. |
| `applyNumericLessThan`    | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр < числа. |
| `applyNumericNotRange`    | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр вне диапазона чисел. |
| `applyNumericRange`       | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по диапазону чисел. |
| `applyOddNumeric`         | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по нечётным числам. |

## Фильтры по строкам

| Метод             | Параметры                                                       | Описание |
|-------------------|-----------------------------------------------------------------|----------|
| `applyArrayString`| array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по массиву строк. |
| `applyDomain`     | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по домену. |
| `applyLike`       | array\|string $field, string $paramName, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | Фильтр LIKE (содержит). |
| `applyLikeEnd`    | array\|string $field, string $paramName, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | Фильтр LIKE (заканчивается). |
| `applyLikeStart`  | array\|string $field, string $paramName, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | Фильтр LIKE (начинается). |
| `applyRegex`      | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по регулярному выражению. |
| `applyString`     | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по строке. |

## Логические фильтры

| Метод                 | Параметры                                                       | Описание |
|-----------------------|-----------------------------------------------------------------|----------|
| `applyBoolean`        | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по булеву значению. |
| `applyEmpty`          | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по пустому значению. |
| `applyExistsByBoolean`| array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр существования по булеву. |
| `applyFalse`          | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр false. |
| `applyNotNull`        | array\|string $field, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр not null. |
| `applyNull`           | array\|string $field, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр null. |
| `applyTrue`           | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр true. |

## Фильтры JSON

| Метод               | Параметры                                                       | Описание |
|---------------------|-----------------------------------------------------------------|----------|
| `applyJsonContains` | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр JSON_CONTAINS. |
| `applyJsonKey`      | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по ключу JSON. |

## Фильтры по отношениям

| Метод                             | Параметры                                                                 | Описание |
|-----------------------------------|---------------------------------------------------------------------------|----------|
| `applyCrossUponCrossWhereHasWhere`| DeepWhereHasWhereParam $paramsDtoFirst, DeepWhereHasWhereParam $paramsDtoSecond, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | Кросс-комбинация whereHas. |
| `applyDeepWhereHasWhere`          | DeepWhereHasWhereParams $paramsDto, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Глубокий whereHas. |
| `applyExcludeByNestedRelation`    | string $relationship, string $nestedRelationship, string $field, ?string $paramName = null, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | Исключение по вложенному отношению. |
| `applyHasRelation`                | string $relationship, ?string $paramName = null, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | Фильтр наличия отношения. |
| `applyHasRelationCount`           | string $relationship, ?string $paramName = null, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | Фильтр по количеству отношений. |
| `applyWhereHas`                   | string $relationship, Closure $callback, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | Базовый whereHas. |
| `applyWhereHasLike`               | string $relationship, string $field, ?string $paramName = null, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | whereHas с LIKE. |
| `applyWhereHasLikeArray`          | string $relationship, array\|string $field, ?string $paramName = null, bool $useOrWhereInArray = true, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | whereHas с LIKE для массива. |
| `applyWhereHasNull`               | string $relationship, string $field, bool $invert = false, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | whereHas с null. |
| `applyWhereHasWhere`              | string $relationship, string $field, ?string $paramName = null, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | whereHas с where. |
| `applyWhereHasWhereIn`            | string $relationship, string $field, ?string $paramName = null, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | whereHas с whereIn. |

## Геолокационные фильтры

| Метод                 | Параметры                                                                 | Описание |
|-----------------------|---------------------------------------------------------------------------|----------|
| `applyGeoBoundingBox` | string $latitudeField, string $longitudeField, ?string $paramName = null, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | Фильтр по bounding box. |
| `applyGeoRadius`      | string $latitudeField, string $longitudeField, ?string $paramName = null, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | Фильтр по радиусу. |

## Специальные фильтры

| Метод             | Параметры                                                       | Описание |
|-------------------|-----------------------------------------------------------------|----------|
| `applyIpAddress`  | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр по IP. |
| `applyRating`     | string $field, ?string $paramName = null, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | Фильтр по рейтингу. |
| `applyStock`      | string $field, ?string $paramName = null, bool $isOrWhere = false, ?EloquentQueryBuilder $q = null | Фильтр по стоку (запасам). |

## Базовые условия

| Метод         | Параметры                                                       | Описание |
|---------------|-----------------------------------------------------------------|----------|
| `applyBetween`| array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Фильтр BETWEEN. |
| `applyWhere`  | array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Базовый WHERE. |
| `applyWhereIn`| array\|string $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | WHERE IN. |

## Вспомогательные методы

| Метод                              | Параметры                                                                 | Описание |
|------------------------------------|---------------------------------------------------------------------------|----------|
| `applyFilter`                      | string $filterClass, string\|array\|null $field = null, ?string $paramName = null, mixed $options = [], ?EloquentQueryBuilder $q = null, bool $valueCanBeNull = false | Применяет указанный фильтр. |
| `applyOrWhereGrouped`              | callable $callback, bool $isOrWhere = false                               | Группирует условия в OR/AND. |
| `applyRecursiveMethod`             | string $method, array $field, ?string $paramName = null, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Рекурсивно применяет метод к массиву. (private) |
| `applyRecursiveMethodWithoutParam` | string $method, array $field, bool $isOrWhere = false, bool $useOrWhereInArray = false, ?EloquentQueryBuilder $q = null | Рекурсивно применяет метод без параметра. (private) |
| `tap`                              | callable $callback                                                        | Выполняет callback на объекте. |

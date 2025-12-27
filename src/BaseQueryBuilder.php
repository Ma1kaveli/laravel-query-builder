<?php

namespace QueryBuilder;

use QueryBuilder\DTO\AvailableSorts;
use QueryBuilder\DTO\DeepWhereHasWhereParam;
use QueryBuilder\DTO\DeepWhereHasWhereParams;

use QueryBuilder\Filters\Combine\ApplyCrossUponCrossWhereHasWhere;
use QueryBuilder\Filters\Combine\ApplyDeepWhereHasWhere;
use QueryBuilder\Filters\Combine\ApplySortByRelationField;
use QueryBuilder\Filters\Combine\ApplyWhereHasLikeArray;
use QueryBuilder\Filters\Combine\ApplyWhereHasWhere;
use QueryBuilder\Filters\Combine\ApplyWhereHasWhereIn;
use QueryBuilder\Filters\Combine\ApplyWhereHasLike;
use QueryBuilder\Filters\Combine\ApplyWhereHasNull;

use QueryBuilder\Filters\Custom\ApplyWhereNotMe;
use QueryBuilder\Filters\Custom\ApplyArchived;

use QueryBuilder\Filters\Datetime\ApplyDateNotRange;
use QueryBuilder\Filters\Datetime\ApplyDay;
use QueryBuilder\Filters\Datetime\ApplyLastYear;
use QueryBuilder\Filters\Datetime\ApplyToday;
use QueryBuilder\Filters\Datetime\ApplyDateEnd;
use QueryBuilder\Filters\Datetime\ApplyDateStart;
use QueryBuilder\Filters\Datetime\ApplyArrayDate;
use QueryBuilder\Filters\Datetime\ApplyArrayTime;
use QueryBuilder\Filters\Datetime\ApplyDate;
use QueryBuilder\Filters\Datetime\ApplyDateRange;
use QueryBuilder\Filters\Datetime\ApplyHour;
use QueryBuilder\Filters\Datetime\ApplyLastMonth;
use QueryBuilder\Filters\Datetime\ApplyLastWeek;
use QueryBuilder\Filters\Datetime\ApplyMinute;
use QueryBuilder\Filters\Datetime\ApplyMonth;
use QueryBuilder\Filters\Datetime\ApplyCurrentHour;
use QueryBuilder\Filters\Datetime\ApplyCurrentMinute;
use QueryBuilder\Filters\Datetime\ApplyTime;
use QueryBuilder\Filters\Datetime\ApplyTimeEnd;
use QueryBuilder\Filters\Datetime\ApplyTimeRange;
use QueryBuilder\Filters\Datetime\ApplyTimeStart;
use QueryBuilder\Filters\Datetime\ApplyYear;

use QueryBuilder\Filters\Geo\ApplyGeoRadius;

use QueryBuilder\Filters\Logic\ApplyBoolean;
use QueryBuilder\Filters\Logic\ApplyNotNull;
use QueryBuilder\Filters\Logic\ApplyNull;
use QueryBuilder\Filters\Logic\ApplyEmpty;
use QueryBuilder\Filters\Logic\ApplyFalse;
use QueryBuilder\Filters\Logic\ApplyTrue;
use QueryBuilder\Filters\Logic\ApplyExistsByBoolean;

use QueryBuilder\Filters\Numeric\ApplyArrayInteger;
use QueryBuilder\Filters\Numeric\ApplyOddNumeric;
use QueryBuilder\Filters\Numeric\ApplyInteger;
use QueryBuilder\Filters\Numeric\ApplyArrayDouble;
use QueryBuilder\Filters\Numeric\ApplyArrayFloat;
use QueryBuilder\Filters\Numeric\ApplyArrayNumeric;
use QueryBuilder\Filters\Numeric\ApplyDouble;
use QueryBuilder\Filters\Numeric\ApplyEvenNumeric;
use QueryBuilder\Filters\Numeric\ApplyFloat;
use QueryBuilder\Filters\Numeric\ApplyMultipleOf;
use QueryBuilder\Filters\Numeric\ApplyNumericRange;
use QueryBuilder\Filters\Numeric\ApplyNumeric;
use QueryBuilder\Filters\Numeric\ApplyNumericGreaterThan;
use QueryBuilder\Filters\Numeric\ApplyNumericLessThan;
use QueryBuilder\Filters\Numeric\ApplyNumericNotRange;

use QueryBuilder\Filters\String\ApplyLikeEnd;
use QueryBuilder\Filters\String\ApplyString;
use QueryBuilder\Filters\String\ApplyArrayString;
use QueryBuilder\Filters\String\ApplyLike;
use QueryBuilder\Filters\String\ApplyJsonContains;
use QueryBuilder\Filters\String\ApplyJsonKey;
use QueryBuilder\Filters\String\ApplyLikeStart;
use QueryBuilder\Filters\String\ApplyRegex;

use QueryBuilder\Filters\System\ApplySortBy;
use QueryBuilder\Filters\System\ApplyWith;
use QueryBuilder\Filters\System\ApplyWithCount;
use QueryBuilder\Filters\System\ApplyWithDeleted;
use QueryBuilder\Filters\System\ApplyOnlyDeleted;
use QueryBuilder\Filters\System\ApplyBetween;
use QueryBuilder\Filters\System\ApplyWhere;
use QueryBuilder\Filters\System\ApplyWhereIn;
use QueryBuilder\Filters\System\ApplyDistinct;
use QueryBuilder\Filters\System\ApplyInRandomOrder;
use QueryBuilder\Filters\System\ApplyLimit;
use QueryBuilder\Filters\System\ApplySelect;
use QueryBuilder\Filters\System\ApplyWhereHas;

use QueryBuilder\Filters\Relation\ApplyHasRelation;
use QueryBuilder\Filters\Relation\ApplyRelationCount;
use QueryBuilder\Filters\Relation\ApplyExcludeByNestedRelation;

use QueryBuilder\Filters\Special\ApplyDomain;
use QueryBuilder\Filters\Special\ApplyRating;
use QueryBuilder\Filters\Special\ApplyIpAddress;
use QueryBuilder\Filters\Special\ApplyStock;

use QueryBuilder\Filters\PaginateFinalizer;
use QueryBuilder\Filters\GetFinalizer;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Closure;

abstract class BaseQueryBuilder
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @var EloquentQueryBuilder|QueryBuilder|LengthAwarePaginator
     */
    protected EloquentQueryBuilder|QueryBuilder|LengthAwarePaginator $query;

    /**
     * @param EloquentQueryBuilder|QueryBuilder $query
     * @param array $params
     */
    public function __construct(
        EloquentQueryBuilder|QueryBuilder $query,
        array $params = []
    ) {
        $this->params = $params;
        $this->query = $query;
    }

    /**
     * get
     *
     * @param array|string $columns = ['*']
     *
     * @return array|EloquentCollection|SupportCollection
     */
    protected function get(array|string $columns = ['*']): array|EloquentCollection|SupportCollection
    {
        return (new GetFinalizer())->apply($this->query, $columns);
    }

    /**
     * all
     *
     * @param array|string $columns = ['*']
     *
     * @return array
     */
    protected function all(array|string $columns = ['*']): array
    {
        return $this->get($columns)->all();
    }

    /**
     * first
     *
     * @param array|string $columns = ['*']
     *
     * @return mixed
     */
    protected function first(array|string $columns = ['*'])
    {
        return $this->query->first($columns);
    }

    /**
     * latest
     *
     * @param array|string $columns = ['*']
     *
     * @return mixed
     */
    protected function latest(array|string $columns = ['*'])
    {
        return $this->query->latest($columns);
    }

    /**
     * Summary of tap
     *
     * @param callable(self): void $callback
     *
     * @return \QueryBuilder\BaseQueryBuilder
     */
    public function tap(callable $callback): static
    {
        $callback($this);
        return $this;
    }

    /**
     * applyPaginate
     *
     * @param bool $canAllRows = false
     * @param array|string $columns = ['*']
     * @param string $rowsPerPageName = 'rows_per_page'
     * @param string $pageName = 'page'
     *
     * @return LengthAwarePaginator
     */
    protected function applyPaginate(
        bool $canAllRows = false,
        array|string $columns = ['*'],
        string $rowsPerPageName = 'rows_per_page',
        string $pageName = 'page',
    ): LengthAwarePaginator {
        $options = [
            'rows_per_page' => $this->params[$rowsPerPageName],
            'can_all_rows' => $canAllRows,
            'columns' => $columns,
            'page_name' => $pageName
        ];

        return (new PaginateFinalizer())->apply($this->query, $options);
    }

    /**
     * withCount
     *
     * @param array|string $relationships
     *
     * @return void
     */
    protected function withCount(array|string $relationships): void
    {
        (new ApplyWithCount())->apply(
            $this->query,
            null,
            $relationships,
        );
    }

    /**
     * with
     *
     * @param array|string $relationships
     *
     * @return void
     */
    protected function with(array|string $relationships): void
    {
        (new ApplyWith())->apply(
            $this->query,
            null,
            $relationships,
        );
    }

    /**
     * inRandomOrder
     *
     * @return void
     */
    protected function inRandomOrder(): void
    {
        (new ApplyInRandomOrder())->apply(
            $this->query,
            null,
            null,
        );
    }

    /**
     * applyWithDeleted
     *
     * @param string $paramName = 'show_deleted'
     *
     * @return void
     */
    public function applyWithDeleted(string $paramName = 'show_deleted')
    {
        $this->applyFilter(
            ApplyWithDeleted::class,
            null,
            $paramName,
        );
    }

    /**
     * Summary of applyOnlyDeleted
     *
     * @param string $paramName = 'only_deleted'
     *
     * @return void
     */
    public function applyOnlyDeleted(string $paramName = 'only_deleted')
    {
        $this->applyFilter(
            ApplyOnlyDeleted::class,
            null,
            $paramName,
        );
    }

    /**
     * applyLimit
     *
     * @param string $paramName = 'limt
     *
     * @return void
     */
    protected function applyLimit(string $paramName = 'limit'): void {
        $this->applyFilter(
            ApplyLimit::class,
            null,
            $paramName,
        );
    }

    /**
     * applySelect
     *
     * @param array<string> $columns = ['*']
     *
     * @return void
     */
    protected function applySelect(array $columns = ['*']): void {
        (new ApplySelect())->apply(
            $this->query,
            null,
            $columns,
        );
    }

    /**
     * applyDistinct
     *
     * @return void
     */
    protected function applyDistinct(): void
    {
        (new ApplyDistinct())->apply(
            $this->query,
            null,
            null,
        );
    }

    /**
     * sortBy
     *
     * @param array<string> $availableSorts
     * @param ?string $defaultField = 'id'
     * @param string $paramName = 'sort_by'
     * @param string $directionParamName = 'descending'
     *
     * @return void
     */
    protected function sortBy(
        array $availableSorts,
        ?string $defaultField = 'id',
        string $paramName = 'sort_by',
        string $directionParamName = 'descending',
    ): void {
        $sortBy = $this->params[$paramName];
        $descending = $this->params[$directionParamName];

        if ($sortBy) {
            (new ApplySortBy())->apply(
                $this->query,
                null,
                null,
                [
                    'sort_by' => $sortBy,
                    'descending' => $descending,
                    'available_sorts' => $availableSorts,
                    'default_field' => $defaultField,
                ],
            );
        }
    }

    /**
     * sortByRelationField
     *
     * @param AvailableSorts $availableSorts
     * @param string $ownerTable
     * @param string $paramName = 'sort_by'
     * @param string $directionParamName = 'descending'
     * @param array<string> $columns = ['*']
     *
     * @return void
     */
    protected function sortByRelationField(
        AvailableSorts $availableSorts,
        string $ownerTable,
        string $paramName = 'sort_by',
        string $directionParamName = 'descending',
        array $columns = ['*']
    ): void {
        $sortBy = $this->params[$paramName];
        $descending = $this->params[$directionParamName];

        if (!$sortBy) {
            (new ApplySortByRelationField())->apply(
                $this->query,
                null,
                null,
                [
                    'sort_by' => $sortBy,
                    'descending' => $descending,
                    'available_sorts' => $availableSorts,
                    'columns' => $columns,
                    'owner_table' => $ownerTable,
                ],
            );
        }
    }

    /**
     * applyWhereNotMe
     *
     * @param string $field = 'id'
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    public function applyWhereNotMe(
        string $field = 'id',
        ?EloquentQueryBuilder $q = null,
    ): void {
        (new ApplyWhereNotMe())->apply(
            query: $q ?? $this->query,
            field: $field,
            value: null,
        );
    }

    /**
     * Summary of applyArchived
     *
     * @param string $field
     * @param ?EloquentQueryBuilder $q $q
     *
     * @return void
     */
    public function applyArchived(
        string $field = 'archived',
        ?EloquentQueryBuilder $q = null,
    ): void {
        $this->applyFilter(
            filterClass: ApplyArchived::class,
            field: $field,
            paramName: null,
            options: [],
            q: $q,
            valueCanBeNull: true
        );
    }

    /**
     * applyInteger
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyInteger(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyInteger',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyInteger::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * Summary of applyExistsByBoolean
     *
     * @param string $field
     * @param string $filterableField
     * @param ?string $paramName
     * @param bool $invert
     * @param bool $isOrWhere
     * @param ?EloquentQueryBuilder $q
     *
     * @return void
     */
    protected function applyExistsByBoolean(
        string $field,
        string $filterableField,
        ?string $paramName = null,
        bool $invert = false,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        $options = [
            'invert' => $invert,
            'filterable_field' => $filterableField,
            'is_or_where' => $isOrWhere
        ];

        $this->applyFilter(
            filterClass: ApplyExistsByBoolean::class,
            field: $field,
            paramName: $paramName,
            options: $options,
            q: $q
        );
    }

    /**
     * Summary of applyWhereHas
     *
     * @param string $relationship
     * @param Closure $subQuery
     * @param bool $isOrWhere
     * @param ?EloquentQueryBuilder $q $q
     *
     * @return void
     */
    protected function applyWhereHas(
        string $relationship,
        Closure $subQuery,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        $options = [
            'is_or_where' => $isOrWhere,
            'sub_query' => $subQuery,
        ];

        $this->applyFilter(
            filterClass: ApplyWhereHas::class,
            field: $relationship,
            paramName: null,
            options: $options,
            q: $q,
            valueCanBeNull: true
        );
    }

    /**
     * Summary of applyWhereHasNull
     *
     * @param string $relationship
     * @param array|string $field
     * @param bool $isOrWhere
     * @param bool $invert
     * @param ?EloquentQueryBuilder $q $q
     *
     * @return void
     */
    protected function applyWhereHasNull(
        string $relationship,
        array|string $field,
        bool $isOrWhere = false,
        bool $invert = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        $this->applyFilter(
            filterClass: ApplyWhereHasNull::class,
            field: $field,
            paramName: null,
            options: [
                'is_or_where' => $isOrWhere,
                'relationship' => $relationship,
                'invert' => $invert,
            ],
            q: $q,
            valueCanBeNull: true
        );
    }

    /**
     * Summary of applyExcludeByNestedRelation
     *
     * @param string $field
     * @param string $relation
     * @param string $nestedRelation
     * @param ?string $paramName
     * @param bool $isOrWhere
     * @param ?EloquentQueryBuilder $q $q
     *
     * @return void
     */
    protected function applyExcludeByNestedRelation(
        string $field,
        string $relation,
        string $nestedRelation,
        ?string $paramName = null,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        $options = [
            'relation' => $relation,
            'nested_relation' => $nestedRelation,
            'is_or_where' => $isOrWhere
        ];

        $this->applyFilter(
            filterClass: ApplyExcludeByNestedRelation::class,
            field: $field,
            paramName: $paramName,
            options: $options,
            q: $q
        );
    }

    /**
     * applyArrayInteger
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyArrayInteger(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyArrayInteger',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyArrayInteger::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyFloat
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyFloat(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyFloat',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyFloat::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyArrayFloat
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyArrayFloat(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyArrayFloat',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyArrayFloat::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyDouble
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyDouble(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyDouble',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyDouble::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyArrayDouble
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyArrayDouble(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyArrayDouble',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyArrayDouble::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyNumeric
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyNumeric(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyNumeric',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyNumeric::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyStock
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyStock(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyStock',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyStock::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyRating
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyRating(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyRating',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyRating::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyIpAddress
     *
     * @param string $field
     * @param string $ipParamName = 'ip'
     * @param string $cidrParamName = 'cidr'
     * @param bool $isOrWhere = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyIpAddress(
        string $field,
        string $ipParamName = 'ip',
        string $cidrParamName = 'cidr',
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        $options = [
            'ip' => $this->params[$ipParamName],
            'cidr' => $this->params[$cidrParamName],
            'is_or_where' => $isOrWhere,
        ];

        (new ApplyIpAddress())->apply(
            $q ?? $this->query,
            $field,
            null,
            $options
        );
    }

    /**
     * applyDomain
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyDomain(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyDomain',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyDomain::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyArrayNumeric
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyArrayNumeric(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyArrayNumeric',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyArrayNumeric::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyNumericRange
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyNumericRange(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyNumericRange',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyNumericRange::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyNumericNotRange
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyNumericNotRange(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyNumericNotRange',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyNumericNotRange::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyNumericGreaterThan
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyNumericGreaterThan(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyNumericGreaterThan',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyNumericGreaterThan::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyNumericLessThan
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyNumericLessThan(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyNumericLessThan',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyNumericLessThan::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyEvenNumeric
     *
     * @param array|string $field
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyEvenNumeric(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethodWithoutParam(
                'applyEvenNumeric',
                $field,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        (new ApplyEvenNumeric())->apply(
            $q ?? $this->query,
            $field,
            null,
            ['is_or_where' => $isOrWhere],
        );
    }

    /**
     * applyOddNumeric
     *
     * @param array|string $field
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyOddNumeric(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethodWithoutParam(
                'applyOddNumeric',
                $field,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        (new ApplyOddNumeric())->apply(
            $q ?? $this->query,
            $field,
            null,
            ['is_or_where' => $isOrWhere],
        );
    }

    /**
     * applyMultipleOf
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyMultipleOf(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyMultipleOf',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyMultipleOf::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyString
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    public function applyString(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyString',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyString::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyArrayString
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    public function applyArrayString(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {

        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyArrayString',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyArrayString::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyBoolean
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    public function applyBoolean(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyBoolean',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyBoolean::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyTrue
     *
     * @param array|string $field
     * @param bool $isOrWhere
     * @param bool $useOrWhereInArray
     * @param ?EloquentQueryBuilder $q
     *
     * @return void
     */
    public function applyTrue(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                method: 'applyTrue',
                field: $field,
                paramName: null,
                isOrWhere: $isOrWhere,
                useOrWhereInArray: $useOrWhereInArray,
                q: $q,
            );

            return;
        }

        $this->applyFilter(
            filterClass: ApplyTrue::class,
            field: $field,
            paramName: null,
            options: ['is_or_where' => $isOrWhere],
            q: $q,
            valueCanBeNull: true
        );
    }

    /**
     * Summary of applyFalse
     *
     * @param array|string $field
     * @param bool $isOrWhere
     * @param bool $useOrWhereInArray
     * @param ?EloquentQueryBuilder $q
     *
     * @return void
     */
    public function applyFalse(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                method: 'applyFalse',
                field: $field,
                paramName: null,
                isOrWhere: $isOrWhere,
                useOrWhereInArray: $useOrWhereInArray,
                q: $q,
            );

            return;
        }

        $this->applyFilter(
            filterClass: ApplyFalse::class,
            field: $field,
            paramName: null,
            options: ['is_or_where' => $isOrWhere],
            q: $q,
            valueCanBeNull: true
        );
    }

    /**
     * applyNull
     *
     * @param array|string $field
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyNull(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethodWithoutParam(
                'applyNull',
                $field,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        (new ApplyNull())->apply(
            $q ?? $this->query,
            $field,
            null,
            ['is_or_where' => $isOrWhere],
        );
    }

    /**
     * applyNotNull
     *
     * @param array|string $field
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyNotNull(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethodWithoutParam(
                'applyNotNull',
                $field,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        (new ApplyNotNull())->apply(
            $q ?? $this->query,
            $field,
            null,
            ['is_or_where' => $isOrWhere]
        );
    }

    /**
     * applyEmpty
     *
     * @param array|string $field
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyEmpty(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethodWithoutParam(
                'applyEmpty',
                $field,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        (new ApplyEmpty())->apply(
            $q ?? $this->query,
            $field,
            null,
            ['is_or_where' => $isOrWhere]
        );
    }

    /**
     * applyHasRelation
     *
     * @param array|string $field
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyHasRelation(
        array|string $field,
        ?EloquentQueryBuilder $q = null
    ): void {
        (new ApplyHasRelation())->apply(
            $q ?? $this->query,
            $field,
            null,
        );
    }

    /**
     * applyRelationCount
     *
     * @param array|string $field
     * @param string $operator
     * @param string $count
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyHasRelationCount(
        array|string $field,
        string $operator = '>=',
        string $count = '1',
        ?EloquentQueryBuilder $q = null
    ): void {
        (new ApplyRelationCount())->apply(
            $q ?? $this->query,
            $field,
            null,
            [
                'operator' => $operator,
                'count' => $count
            ]
        );
    }

    /**
     * applyGeoRadius
     *
     * @param string $longitudeField
     * @param string $latitudeField
     * @param string $radiusField
     * @param ?string $longitudeParamName = null
     * @param ?string $latitudeParamName = null
     * @param ?string $radiusParamName = null
     * @param bool $isOrWhere = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyGeoRadius(
        string $longitudeField,
        string $latitudeField,
        string $radiusField,
        ?string $longitudeParamName = null,
        ?string $latitudeParamName = null,
        ?string $radiusParamName = null,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        $longitudeParamName ??= $longitudeField;
        $latitudeParamName ??= $latitudeField;
        $radiusParamName ??= $radiusField;

        $options = [
            'lon_field' => $longitudeField,
            'lat_field' => $latitudeField,
            'lon' => $this->params[$longitudeParamName] ?? null,
            'lat' => $this->params[$latitudeParamName] ?? null,
            'radius' => $this->params[$radiusParamName] ?? null,
            'is_or_where' => $isOrWhere
        ];

        (new ApplyGeoRadius())->apply(
            $q ?? $this->query,
            null,
            null,
            $options
        );
    }

    /**
     * applyGeoBoundingBox
     *
     * @param string $longitudeField
     * @param string $latitudeField
     * @param ?string $minLongitudeParamName = null
     * @param ?string $minLatitudeParamName = null
     * @param ?string $maxLongitudeParamName = null
     * @param ?string $maxLatitudeParamName = null
     * @param bool $isOrWhere = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyGeoBoundingBox(
        string $longitudeField,
        string $latitudeField,
        ?string $minLongitudeParamName = null,
        ?string $minLatitudeParamName = null,
        ?string $maxLongitudeParamName = null,
        ?string $maxLatitudeParamName = null,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        $minLongitudeParamName ??= `min_{$longitudeField}`;
        $minLatitudeParamName ??= `min_{$latitudeField}`;
        $maxLongitudeParamName ??= `max_{$longitudeField}`;
        $maxLatitudeParamName ??= `max_{$latitudeField}`;

        $options = [
            'lon_field' => $longitudeField,
            'lat_field' => $latitudeField,
            'min_lon' => $this->params[$minLongitudeParamName] ?? null,
            'min_lat' => $this->params[$minLatitudeParamName] ?? null,
            'max_lon' => $this->params[$maxLongitudeParamName] ?? null,
            'max_lat' => $this->params[$maxLatitudeParamName] ?? null,
            'is_or_where' => $isOrWhere
        ];

        (new ApplyGeoRadius())->apply(
            $q ?? $this->query,
            null,
            null,
            $options
        );
    }

    /**
     * applyDateStart
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyDateStart(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyDateStart',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        if (!$paramName) {
            $paramName = "{$field}_start";
        }

        $this->applyFilter(
            ApplyDateStart::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyDateEnd
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyDateEnd(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyDateEnd',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        if (!$paramName) {
            $paramName = "{$field}_end";
        }

        $this->applyFilter(
            ApplyDateEnd::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyDateStartEnd
     *
     * @param array|string $field
     * @param ?string $paramStartName = null
     * @param ?string $paramEndName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyDateStartEnd(
        array|string $field,
        ?string $paramStartName = null,
        ?string $paramEndName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            foreach ($field as $key => $el) {
                $isFirst = $key === 0;
                $this->applyDateStartEnd(
                    $el,
                    $paramStartName,
                    $paramEndName,
                    $isFirst ? $isOrWhere : true,
                    $useOrWhereInArray,
                    $q
                );
            }

            return;
        }

        if (!$paramStartName) {
            $paramStartName = "{$field}_start";
        }

        if (!$paramEndName) {
            $paramEndName = "{$field}_end";
        }

        $this->applyFilter(
            ApplyDateStart::class,
            $field,
            $paramStartName,
            ['is_or_where' => $isOrWhere],
            $q
        );

        $this->applyFilter(
            ApplyDateEnd::class,
            $field,
            $paramEndName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyDate
     *
     * @param array|string $field
     * @param ?string $paramName
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyDate(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyDate',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyDate::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyArrayDate
     *
     * @param array|string $field
     * @param ?string $paramName
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyArrayDate(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyArrayDate',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyArrayDate::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyYear
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyYear(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyYear',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyYear::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyMonth
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyMonth(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyMonth',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyMonth::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyDay
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyDay(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyDay',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyDay::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyDateRange
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyDateRange(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyDateRange',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyDateRange::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyDateNotRange
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyDateNotRange(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyDateNotRange',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyDateNotRange::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyToday
     *
     * @param array|string $field
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyToday(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethodWithoutParam(
                'applyToday',
                $field,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        (new ApplyToday())->apply(
            $q ?? $this->query,
            $field,
            null,
            ['is_or_where' => $isOrWhere]
        );
    }

    /**
     * applyLastWeek
     *
     * @param array|string $field
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyLastWeek(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethodWithoutParam(
                'applyLastWeek',
                $field,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        (new ApplyLastWeek())->apply(
            $q ?? $this->query,
            $field,
            null,
            ['is_or_where' => $isOrWhere]
        );
    }

    /**
     * applyLastMonth
     *
     * @param array|string $field
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyLastMonth(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethodWithoutParam(
                'applyLastMonth',
                $field,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        (new ApplyLastMonth())->apply(
            $q ?? $this->query,
            $field,
            null,
            ['is_or_where' => $isOrWhere]
        );
    }

    /**
     * applyLastYear
     *
     * @param array|string $field
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyLastYear(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethodWithoutParam(
                'applyLastYear',
                $field,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        (new ApplyLastYear())->apply(
            $q ?? $this->query,
            $field,
            null,
            ['is_or_where' => $isOrWhere],
        );
    }

    /**
     * applyTimeStart
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyTimeStart(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyTimeStart',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        if (!$paramName) {
            $paramName = "{$field}_start";
        }

        $this->applyFilter(
            ApplyTimeStart::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyTimeEnd
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyTimeEnd(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyTimeEnd',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        if (!$paramName) {
            $paramName = "{$field}_end";
        }

        $this->applyFilter(
            ApplyTimeEnd::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyTimeStartEnd
     *
     * @param array|string $field
     * @param ?string $paramStartName = null
     * @param ?string $paramEndName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyTimeStartEnd(
        array|string $field,
        ?string $paramStartName = null,
        ?string $paramEndName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            foreach ($field as $key => $el) {
                $isFirst = $key === 0;
                $this->applyTimeStartEnd(
                    $el,
                    $paramStartName,
                    $paramEndName,
                    $isFirst ? $isOrWhere : true,
                    $useOrWhereInArray,
                    $q
                );
            }

            return;
        }

        if (!$paramStartName) {
            $paramStartName = "{$field}_start";
        }

        if (!$paramEndName) {
            $paramEndName = "{$field}_end";
        }

        $this->applyFilter(
            ApplyTimeStart::class,
            $field,
            $paramStartName,
            ['is_or_where' => $isOrWhere],
            $q
        );

        $this->applyFilter(
            ApplyTimeEnd::class,
            $field,
            $paramEndName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyTime
     *
     * @param array|string $field
     * @param ?string $paramName
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyTime(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyTime',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyTime::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyArrayTime
     *
     * @param array|string $field
     * @param ?string $paramName
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyArrayTime(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyArrayTime',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyArrayTime::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyCurrentMinute
     *
     * @param array|string $field
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyCurrentMinute(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethodWithoutParam(
                'applyCurrentMinute',
                $field,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        (new ApplyCurrentMinute())->apply(
            $q ?? $this->query,
            $field,
            null,
            ['is_or_where' => $isOrWhere]
        );
    }

    /**
     * applyCurrentHour
     *
     * @param array|string $field
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyCurrentHour(
        array|string $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethodWithoutParam(
                'applyCurrentHour',
                $field,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        (new ApplyCurrentHour())->apply(
            $q ?? $this->query,
            $field,
            null,
            ['is_or_where' => $isOrWhere]
        );
    }

    /**
     * applyTimeRange
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyTimeRange(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyTimeRange',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyTimeRange::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyMinute
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyMinute(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyMinute',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyMinute::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyHour
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyHour(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyHour',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyHour::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyLike
     *
     * @param array|string $field
     * @param string $paramName
     * @param bool $isOrWhere = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyLike(
        array|string $field,
        string $paramName,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        $this->applyFilter(
            ApplyLike::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyLikeStart
     *
     * @param array|string $field
     * @param string $paramName
     * @param bool $isOrWhere = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyLikeStart(
        array|string $field,
        string $paramName,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        $this->applyFilter(
            ApplyLikeStart::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyLikeEnd
     *
     * @param array|string $field
     * @param string $paramName
     * @param bool $isOrWhere = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyLikeEnd(
        array|string $field,
        string $paramName,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        $this->applyFilter(
            ApplyLikeEnd::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyRegex
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false,
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyRegex(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyRegex',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyRegex::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyJsonContains
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false,
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyJsonContains(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyJsonContains',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyJsonContains::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyJsonKey
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false,
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyJsonKey(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyJsonKey',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyJsonKey::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyWhereHasLike
     *
     * @param string $relationship
     * @param string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyWhereHasLike(
        string $relationship,
        string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        $this->applyFilter(
            ApplyWhereHasLike::class,
            $field,
            $paramName,
            [
                'is_or_where' => $isOrWhere,
                'relationship' => $relationship
            ],
            $q
        );
    }

    /**
     * applyWhere
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyWhere(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyWhere',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyWhere::class,
            $field,
            $paramName,
            ['is_or_where' => $isOrWhere],
            $q
        );
    }

    /**
     * applyWhereIn
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyWhereIn(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyWhereIn',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyWhereIn::class,
            $field,
            $paramName,
            [
                'is_or_where' => $isOrWhere
            ],
            $q
        );
    }

    /**
     * applyBetween
     *
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyBetween(
        array|string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        if (is_array($field)) {
            $this->applyRecursiveMethod(
                'applyBetween',
                $field,
                $paramName,
                $isOrWhere,
                $useOrWhereInArray,
                $q
            );

            return;
        }

        $this->applyFilter(
            ApplyBetween::class,
            $field,
            $paramName,
            [
                'is_or_where' => $isOrWhere
            ],
            $q
        );
    }

    /**
     * applyWhereHasWhere
     *
     * @param string $relationship
     * @param string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyWhereHasWhere(
        string $relationship,
        string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        $this->applyFilter(
            ApplyWhereHasWhere::class,
            $field,
            $paramName,
            [
                'is_or_where' => $isOrWhere,
                'relationship' => $relationship
            ],
            $q
        );
    }

    /**
     * applyDeepWhereHasWhere
     *
     * @param DeepWhereHasWhereParams $paramsDto
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyDeepWhereHasWhere(
        DeepWhereHasWhereParams $paramsDto,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        foreach ($paramsDto->deepWhereHasWhereParam as $key => $el) {
            $isFirst = $key === 0;
            $this->applyFilter(
                ApplyDeepWhereHasWhere::class,
                $el->field,
                $el->paramName,
                [
                    'relationship' => $el->relationship,
                    'is_deep_or_where' => $el->isDeepOrWhere,
                    'is_or_where' => $isFirst ? $isOrWhere : $useOrWhereInArray
                ],
                $q
            );
        }
    }

    /**
     * applyWhereHasWhereIn
     *
     * @param string $relationship
     * @param string $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyWhereHasWhereIn(
        string $relationship,
        string $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        $this->applyFilter(
            ApplyWhereHasWhereIn::class,
            $field,
            $paramName,
            [
                'is_or_where' => $isOrWhere,
                'relationship' => $relationship
            ],
            $q
        );
    }

    /**
     * applyWhereHasLikeArray
     *
     * @param string $relationship
     * @param array|string $field
     * @param ?string $paramName = null
     * @param bool $useOrWhereInArray = true
     * @param bool $isOrWhere = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyWhereHasLikeArray(
        string $relationship,
        array|string $field,
        ?string $paramName = null,
        bool $useOrWhereInArray = true,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        $this->applyFilter(
            ApplyWhereHasLikeArray::class,
            $field,
            $paramName,
            [
                'is_or_where' => $isOrWhere,
                'relationship' => $relationship,
                'is_deep_or_where' => $useOrWhereInArray
            ],
            $q
        );
    }

    /**
     * applyCrossUponCrossWhereHasWhere
     *
     * @param DeepWhereHasWhereParam $paramsDtoFirst
     * @param DeepWhereHasWhereParam $paramsDtoSecond
     * @param bool $isOrWhere = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    protected function applyCrossUponCrossWhereHasWhere(
        DeepWhereHasWhereParam $paramsDtoFirst,
        DeepWhereHasWhereParam $paramsDtoSecond,
        bool $isOrWhere = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        $term1 = $this->params[
            empty($paramsDtoFirst->paramName)
                ? $paramsDtoFirst->field
                : $paramsDtoFirst->paramName
        ];

        $term2 = $this->params[
            empty($paramsDtoSecond->paramName)
                ? $paramsDtoSecond->field
                : $paramsDtoSecond->paramName
        ];

        (new ApplyCrossUponCrossWhereHasWhere())->apply(
            $q ?? $this->query,
            null,
            null,
            [
                'term_1' => $term1,
                'field_1' => $paramsDtoFirst->field,
                'relationship_1' => $paramsDtoFirst->relationship,
                'term_2' => $term2,
                'field_2' => $paramsDtoSecond->field,
                'relationship_2' => $paramsDtoSecond->relationship,
                'is_or_where' => $isOrWhere
            ],
        );
    }

    /**
     * applyFilter
     *
     * @param string $filterClass
     * @param string|array|null $field = null
     * @param ?string $paramName = null
     * @param mixed $options = []
     * @param ?EloquentQueryBuilder $q = null,
     *
     * @return void
     */
    protected function applyFilter(
        string $filterClass,
        string|array|null $field = null,
        ?string $paramName = null,
        mixed $options = [],
        ?EloquentQueryBuilder $q = null,
        bool $valueCanBeNull = false
    ): void {
        $paramName ??= $field;
        $value = $this->params[$paramName] ?? null;

        if ($value !== null || $valueCanBeNull) {
            (new $filterClass())->apply(
                $q ?? $this->query,
                $field,
                $value,
                $options
            );
        }
    }

    /**
     * applyOrWhereGrouped
     *
     * @param callable $callback
     * @param bool $isOrWhere = false
     *
     * @return void
     */
    protected function applyOrWhereGrouped(
        callable $callback,
        bool $isOrWhere = false,
    ): void {
        $this->query->where(
            function ($groupQuery) use ($callback) {
                $callback($groupQuery);
            },
            null,
            null,
            $isOrWhere ? 'or' : 'and'
        );
    }

    /**
     * applyRecursiveMethod
     *
     * @param string $method
     * @param array $field
     * @param ?string $paramName = null
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    private function applyRecursiveMethod(
        string $method,
        array $field,
        ?string $paramName = null,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null,
    ): void {
        foreach ($field as $key => $el) {
            $isFirst = $key === 0;
            $this->{$method}(
                $el,
                $paramName,
                $isFirst ? $isOrWhere : $useOrWhereInArray,
                $useOrWhereInArray,
                $q
            );
        }
    }

    /**
     * applyRecursiveMethodWithoutParam
     *
     * @param string $method
     * @param array $field
     * @param bool $isOrWhere = false
     * @param bool $useOrWhereInArray = false
     * @param ?EloquentQueryBuilder $q = null
     *
     * @return void
     */
    private function applyRecursiveMethodWithoutParam(
        string $method,
        array $field,
        bool $isOrWhere = false,
        bool $useOrWhereInArray = false,
        ?EloquentQueryBuilder $q = null
    ): void {
        foreach ($field as $key => $el) {
            $isFirst = $key === 0;
            $this->{$method}(
                $el,
                $isFirst ? $isOrWhere : $useOrWhereInArray,
                $useOrWhereInArray,
                $q
            );
        }
    }
}

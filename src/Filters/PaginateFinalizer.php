<?php

namespace LaravelQueryBuilder\Filters;

use LaravelQueryBuilder\Helpers\CheckTypes;
use LaravelQueryBuilder\Interfaces\FinalizerInterface;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaginateFinalizer implements FinalizerInterface
{
    /**
     * apply
     *
     * @param EloquentQueryBuilder|QueryBuilder $query
     * @param array|string $options = []
     *
     * @return LengthAwarePaginator
     */
    public function apply(
        EloquentQueryBuilder|QueryBuilder $query,
        array|string $options = [],
    ): LengthAwarePaginator {
        $rowsPerPage = $options['rows_per_page'];
        $columns =  $options['columns'];
        $canAllRows = $options['can_all_rows'];
        $pageName = $options['page_name'];

        if (
            !CheckTypes::isInteger($rowsPerPage)
            || (!$canAllRows && $rowsPerPage === -1)
        ) {
            $rowsPerPage = 25;
        }

        return $query->paginate(
            $rowsPerPage,
            $columns,
            $pageName,
        );
    }
}

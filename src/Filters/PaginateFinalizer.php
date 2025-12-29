<?php

namespace QueryBuilder\Filters;

use QueryBuilder\Helpers\CheckTypes;
use QueryBuilder\Interfaces\FinalizerInterface;

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
        $maxRowsPerPage = $options['max_rows_per_page'];

        if (
            !CheckTypes::isInteger($rowsPerPage)
            || (!$canAllRows && $rowsPerPage === -1)
            || $rowsPerPage > $maxRowsPerPage
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

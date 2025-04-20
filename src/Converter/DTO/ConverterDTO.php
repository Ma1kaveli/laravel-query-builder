<?php

namespace BaseQueryBuilder\Converter\DTO;

use BaseQueryBuilder\Converter\Constants\CaseConstants;
use BaseQueryBuilder\Converter\CaseConverter;
use BaseQueryBuilder\Helpers\QueryParams;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConverterDTO {
    protected CaseConverter $caseConverter;

    public function __construct()
    {
        $this->caseConverter = new CaseConverter();
    }

    /**
     * getQueryParams
     *
     * @param Request $request
     * @param array $paramNames
     * @param ?array $sortByMapper = null
     * @param string $sortByKey = 'sort_by'
     *
     * @return array
     */
    public static function getQueryParams(
        Request $request,
        array $paramNames,
        ?array $sortByMapper = null,
        string $sortByKey = 'sort_by'
    ): array {
        $params = [];

        $case = CaseConstants::CASE_SNAKE;

        $needConvertSortBy = fn ($value) => ((Str::{$case}($value) === 'sort_by') && empty($sortByMapper));

        foreach ($paramNames as $value) {
            $requestVal = $needConvertSortBy($value)
                ? Str::{$case}($request->input($value))
                : $request->input($value);

            $params[Str::{$case}($value)] = $requestVal;
        }

        if (!empty($sortByMapper)) {
            return QueryParams::mapSortBy($params, $sortByMapper, $sortByKey);
        }

        return $params;
    }

    /**
     * getRequestData
     *
     * @param array $data
     *
     * @return array
     */
    public function getRequestData(array $data): array
    {
        return $this->caseConverter->convert(CaseConstants::CASE_SNAKE, $data);
    }
}

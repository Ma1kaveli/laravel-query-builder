<?php

namespace BaseQueryBuilder\DTO;

use BaseQueryBuilder\Converter\DTO\ConverterDTO;

use Illuminate\Http\Request;

class ListDTO {
    public function __construct(
        public readonly array $params,
    ) {}

    /**
     * @param Request $request
     * @param array $params = []
     * @param array $mapParams = []
     *
     * @return ListDTO
     */
    public static function fromRequest(Request $request, array $params = [], array $mapParams = []): ListDTO {
        $convertDTO = ConverterDTO::getQueryParams(
            $request,
            [
                ...$params,
                'search', 'showDeleted', 'rowsPerPage', 'sortBy', 'descending'
            ],
            $mapParams
        );

        return new self(
            params: [
                ...$convertDTO,
            ],
        );
    }

    /**
     * fromDefault
     *
     * @param array $params = []
     *
     * @return ListDTO
     */
    public static function fromDefault(array $params = []): ListDTO {
        return new self(
            params: $params
        );
    }

    /**
     * appendParams
     *
     * @param array $params
     *
     * @return ListDTO
     */
    public function appendParams(array $params): ListDTO
    {
        return new self(
            params: [
                ...$this->params,
                ...$params
            ]
        );
    }
}

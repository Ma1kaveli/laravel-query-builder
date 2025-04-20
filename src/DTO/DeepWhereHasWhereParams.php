<?php

namespace BaseQueryBuilder\DTO;

class DeepWhereHasWhereParams
{
    /**
     * @var array<DeepWhereHasWhereParam>
     */
    public readonly array $deepWhereHasWhereParam;

    /**
     * __construct
     *
     * @param array<DeepWhereHasWhereParam> $deepWhereHasWhereParam
     *
     * @return void
     */
    public function __construct(array $deepWhereHasWhereParam) {
        $this->deepWhereHasWhereParam = $deepWhereHasWhereParam;
    }
}

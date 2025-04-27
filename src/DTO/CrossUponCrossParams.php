<?php

namespace QueryBuilder\DTO;

class CrossUponCrossParams
{
    /**
     * @var DeepWhereHasWhereParam
     */
    public readonly DeepWhereHasWhereParam $firstElement;

    /**
     * @var DeepWhereHasWhereParam
     */
    public readonly DeepWhereHasWhereParam $secondElement;

    /**
     * __construct
     *
     * @param DeepWhereHasWhereParam $firstElement
     *
     * @return void
     */
    public function __construct(DeepWhereHasWhereParam $firstElement, DeepWhereHasWhereParam $secondElement) {
        $this->firstElement = $firstElement;
        $this->secondElement = $secondElement;
    }
}

<?php

namespace LaravelQueryBuilder\DTO;

class AvailableSorts
{
    /**
     * @var array<AvailableSort>
     */
    public readonly array $availableSorts;

    /**
     * __construct
     *
     * @param array<AvailableSort> $deepWhereHasWhereParam
     *
     * @return void
     */
    public function __construct(array $availableSorts) {
        $this->availableSorts = $availableSorts;
    }
}

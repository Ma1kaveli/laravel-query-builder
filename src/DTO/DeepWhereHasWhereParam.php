<?php

namespace BaseQueryBuilder\DTO;

class DeepWhereHasWhereParam
{
    /**
     * @var string
     */
    public readonly string $relationship;

    /**
     * @var array|string
     */
    public readonly array|string $field;

    /**
     * @var bool
     */
    public readonly bool $isDeepOrWhere;

    /**
     * @var ?string
     */
    public readonly ?string $paramName;

    /**
     * __construct
     *
     * @param string $relationship
     * @param array|string $field
     * @param bool $isDeepOrWhere = false
     * @param ?string $paramName = null
     *
     * @return void
     */
    public function __construct(
        string $relationship,
        array|string $field,
        bool $isDeepOrWhere = false,
        ?string $paramName = null
    ) {
        $this->relationship = $relationship;
        $this->field = $field;
        $this->isDeepOrWhere = $isDeepOrWhere;
        $this->paramName = $paramName;
    }
}

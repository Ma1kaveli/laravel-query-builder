<?php

namespace QueryBuilder\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class PaginatedCollection extends ResourceCollection {

    protected array|Arrayable|JsonSerializable $data;

    public function __construct($resource, array|Arrayable|JsonSerializable $data) {
        parent::__construct($resource);
        $this->data = $data;
    }

    public function toArray($request)
    {
        $paginated = $this->resource->toArray();
        $paginationMap = config('query-builder.pagination_map');

        $result = [
            'data' => $this->data,
        ];

        foreach ($paginationMap as $key => $sourceKey) {
            if (isset($paginated[$sourceKey])) {
                $result[$key] = $paginated[$sourceKey];
            }
        }

        return $result;
    }
}
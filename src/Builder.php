<?php

namespace Lazy\Db;

class Builder
{
    /**
     * @var array
     */
    protected $parts = [

        'select' => [],
        'distinct' => false,
        'from' => '',
        'join' => [],
        'where' => [],
        'groupBy' => [],
        'having' => [],
        'orderBy' => [],
        'limit' => '',
        'offset' => '',
        'union' => []

    ];
}

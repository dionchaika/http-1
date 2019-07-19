<?php

namespace Lazy\Db;

use InvalidArgumentException;

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

    public function where(array $where, $boolean = 'and')
    {
        if (1 === count($where)) {
            [$column, $operator, $value] = [$where[0], 'IS NOT', 'NULL'];
        } else if (2 === count($where)) {
            [$column, $operator, $value] = [$where[0], '=', $where[1]];
        } else if (3 === count($where)) {
            [$column, $operator, $value] = [$where[0], $where[1], $where[2]];
        }

        $this->parts['where'][] = compact('column', 'operator', 'value', 'boolean');

        return $this;
    }

    public function orWhere(array $where)
    {
        return $this->where($where, 'or');
    }

    public function whereIn($column, array $values, $boolean = 'and')
    {
        return $this->where([$column, 'IN', sprintf('(%s)', implode(', ', $values))]);
    }

    public function orWhereIn($column, array $values)
    {
        return $this->where([$column, 'IN', sprintf('(%s)', implode(', ', $values))], 'or');
    }
}

<?php

namespace Lazy\Db;

use PDO;
use Throwable;

class Conn implements ConnInterface
{
    protected $pdo;
    protected $config = [];

    public function __construct(PDO $pdo, array $config = [])
    {
        $this->pdo = $pdo;
        $this->config = $config;
    }

    public function getName()
    {
        return ! empty($this->config['name']) ? $this->config['name'] : 'default';
    }
}

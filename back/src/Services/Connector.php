<?php

namespace Services;

use PDO;

abstract class Connector
{
    /**
     * @var string $connectionString
     */
    protected $connectionString;

    /**
     * @param $connectionString
     */
    public function __construct(string $connectionString) {
        $this->connectionString = $connectionString;
    }

    /**
     * @return PDO
     */
    protected abstract function connect(): PDO;

    /**
     * @param string $query
     *
     * @return array
     */
    public abstract function select(string $query): array;

    /**
     * @param string $query
     *
     * @return int
     */
    public abstract function insert(string $query): int;

    /**
     * @param string $query
     * @param array $parameters
     *
     * @return array
     */
    public abstract function execute(string $query, array $parameters = []);
}
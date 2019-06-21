<?php

namespace Services;

use Exception;
use PDO;

class PgConnector
{
    /**
     * @var string $connectionString
     */
    private $connectionString;

    /**
     * @param $connectionString
     */
    public function __construct(string $connectionString) {
        $this->connectionString = $connectionString;
    }

    /**
     * @return PDO
     */
    private function connect() {
        return new PDO(sprintf("pgsql:%s", $this->connectionString));
    }

    /**
     * @param string $query
     *
     * @return array
     *
     * @throws Exception
     */
    public function select(string $query) {
        $connection = $this->connect();

        $result = $connection->query($query)->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * @param string $query
     *
     * @return void
     */
    public function insert(string $query) {
        $connection = $this->connect();

        $connection->prepare($query)->execute();
    }
}

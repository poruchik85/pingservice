<?php

namespace Services;

use Exception;
use PDO;

class PgConnector extends Connector
{
    /**
     * @return PDO
     */
    protected function connect(): PDO {
        return new PDO(sprintf("pgsql:%s", $this->connectionString));
    }

    /**
     * @param string $query
     *
     * @return array
     */
    public function select(string $query):array {
        $connection = $this->connect();

        try {
            $result = $connection->query($query)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo sprintf("DB connection error: %s", $e->getMessage());

            die();
        }

        return $result;
    }

    /**
     * @param string $query
     *
     * @return int
     */
    public function insert(string $query): int {
        $connection = $this->connect();

        try {
            $result = $connection->query($query)->fetchAll(PDO::FETCH_ASSOC);

            return $result[0]["id"];
        } catch (Exception $e) {
            echo sprintf("DB connection error: %s", $e->getMessage());

            die();
        }
    }

    /**
     * @param string $query
     * @param array $parameters
     *
     * @return array
     */
    public function execute(string $query, array $parameters = []) {
        $connection = $this->connect();

        try {
            $statement = $connection->prepare($query);
            $statement->execute($parameters);

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo sprintf("DB connection error: %s", $e->getMessage());

            die();
        }
    }
}

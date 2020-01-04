<?php

use PHPUnit\Framework\TestCase;
use Services\PgConnector;

class PgConnectorTest extends TestCase
{
    /**
     * @var string $dbConnectionString
     */
    private $dbConnectionString;

    /**
     * @var PgConnector $connector
     */
    private $connector;

    public function setUp(): void
    {
        $this->dbConnectionString = "dbname=pingservice; host=postgresql; port=5432; user=pingservice; password=pingservice";

        $this->connector = new PgConnector($this->dbConnectionString);
    }

    public function testConnector(): void
    {
        $reflector = new ReflectionClass(PgConnector::class);
        $method = $reflector->getMethod("connect");
        $method->setAccessible(true);

        $connection = $method->invokeArgs($this->connector, []);

        $this->assertEquals(
            get_class($connection),
            "PDO"
        );
    }
}

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
     * @var string $wrongDbConnectionString
     */
    private $wrongDbConnectionString;

    /**
     * @var PgConnector $connector
     */
    private $connector;

    public function setUp(): void
    {
        $this->dbConnectionString = "dbname=vladis; host=postgresql; port=5432; user=vladis; password=vladis";
        $this->wrongDbConnectionString = "dbname=vladis; host=po; port=5432; user=vladis; password=vladis";

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

    public function testWrongConnector(): void
    {
        $this->expectException(PDOException::class);

        $connector = new PgConnector($this->wrongDbConnectionString);
        $reflector = new ReflectionClass(PgConnector::class);
        $method = $reflector->getMethod("connect");
        $method->setAccessible(true);

        $method->invokeArgs($connector, []);
    }
}
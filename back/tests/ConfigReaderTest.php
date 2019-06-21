<?php

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Services\ConfigReader;

class ConfigReaderTest extends TestCase
{
    /**
     * @var string $fileSystem
     */
    private $fileSystem;

    public function setUp(): void
    {
        $directory = [
            "config" => [
                "config.json" => "{\"dbConnectionString\": \"dbname=vladis; host=postgresql; port=5432; user=vladis; password=vladis\"}",
                "wrongconfig.json" => "{\"wtf\": \"dbname=vladis; host=postgresql; port=5432; user=vladis; password=vladis\"}",
                "brokenconfig.json" => "{\"dbConnectionString\": \"dbname=vladis; host=postgresql; port=5432; user=vladis; password=vladis\"",
            ]
        ];

        $this->fileSystem = vfsStream::setup("root", 444, $directory);
    }

    public function testGetConfig(): void
    {
        $configPath = sprintf("%s/%s", $this->fileSystem->url(), "config/config.json");
        $config = new ConfigReader($configPath);

        $this->assertEquals(
            "dbname=vladis; host=postgresql; port=5432; user=vladis; password=vladis",
            $config->dbConnectionString
        );
    }

    public function testGetBrokenConfig(): void
    {
        $this->expectException(JsonException::class);

        $configPath = sprintf("%s/%s", $this->fileSystem->url(), "config/brokenconfig.json");
        new ConfigReader($configPath);
    }

    public function testGetWrongConfig(): void
    {
        $this->expectException(LogicException::class);

        $configPath = sprintf("%s/%s", $this->fileSystem->url(), "config/wrongconfig.json");
        new ConfigReader($configPath);
    }

    public function testMissingConfig(): void
    {
        error_reporting(E_ERROR);
        $this->expectException(ErrorException::class);

        $configPath = sprintf("%s/%s", $this->fileSystem->url(), "config/missing.json");
        new ConfigReader($configPath);
    }
}

<?php

/**
 * @property string|null dbConnectionString
 */
class ConfigReader
{
    const CONFIG_PATH = "../config.json";
    const CONFIG_PARAMETERS = [
        "dbConnectionString",
    ];

    private $config;

    /**
     * @param string|null $configPath
     *
     * @throws Exception
     * @throws JsonException
     */
    public function __construct(string $configPath = null) {
        $path = $configPath ?? self::CONFIG_PATH;

        $configContent = file_get_contents($path);
        if ($configContent === false) {
            throw new ErrorException("Config file reading error");
        }

        $this->config = json_decode($configContent, true);

        if ($this->config === null) {
            throw new JsonException(sprintf(
                "Config file contains invalid json [line %d]",
                json_last_error()
            ));
        }

        $this->validateConfig();
    }

    /**
     * @throws Exception
     */
    private function validateConfig() {
        foreach (self::CONFIG_PARAMETERS as $configParameter) {
            if (!isset($this->config[$configParameter])) {
                throw new LogicException("Invalid config parameters");
            }
        }
    }

    /**
     * @param string|null $property
     *
     * @return string|null
     */
    public function __get(string $property = null) {
        if (isset($this->config[$property])) {
            return $this->config[$property];
        }

        return null;
    }
}


<?php

namespace Controllers;

use JsonException;
use Services\ConfigReader;
use Services\Connector;
use Services\DashboardService;
use Services\PgConnector;

class DashboardController
{
    /**
     * @var Connector $connector
     */
    protected $connector;

    /**
     * @throws JsonException
     */
    public function __construct() {
        $config = new ConfigReader();

        $this->connector = new PgConnector($config->dbConnectionString);
    }

    /**
     * @return false|string
     */
    public function indexAction() {
        $dashboardService = new DashboardService($this->connector);

        return json_encode($dashboardService->getData());
    }

    /**
     * @return false|string
     */
    public function pingAction() {
        $dashboardService = new DashboardService($this->connector);

        $dashboardService->ping();

        return json_encode($dashboardService->getData());
    }
}

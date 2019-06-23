<?php

namespace Controllers;

use JsonException;
use Models\Server;
use Services\ConfigReader;
use Services\Connector;
use Services\DashboardService;
use Services\PgConnector;
use Services\Router\Request;

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
     * @param Request $request
     * @param array $parameters
     *
     * @return false|string
     */
    public function pingAction(Request $request, array $parameters) {
        /** @var Server $server */
        $server = Server::find($parameters["id"]);

        $dashboardService = new DashboardService($this->connector);

        $dashboardService->ping($server);

        return "";
    }
}

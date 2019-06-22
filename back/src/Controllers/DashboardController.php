<?php

namespace Controllers;

use JsonException;
use Models\Group;
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

        $data = array_reduce($dashboardService->getData(), function ($v, $i) {
            if (!isset($v[$i["group_id"]])) {
                $v[$i["group_id"]] = [
                    "id" => $i["group_id"],
                    "name" => $i["group_name"],
                    "created_at" => $i["group_created_at"],
                    "servers" => [],
                ];
            }
            if (!isset($v[$i["group_id"]]["servers"][$i["server_id"]])) {
                $v[$i["group_id"]]["servers"][$i["server_id"]] = [
                    "id" => $i["server_id"],
                    "host" => $i["host"],
                    "ip" => $i["ip"],
                    "created_at" => $i["server_created_at"],
                    "pings" => [],
                ];
            }
            $v[$i["group_id"]]["servers"][$i["server_id"]]["pings"][$i["ping_id"]] = [
                "success" => $i["success"],
                "created_at" => $i["ping_created_at"],
            ];

            return $v;
        }, []);

        return json_encode($data);
    }
}

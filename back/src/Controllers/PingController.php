<?php

namespace Controllers;

use Models\Ping;
use Services\Router\Request;

class PingController
{
    /**
     * @return false|string
     */
    public function listAction() {
        $servers = Ping::list();

        return json_encode($servers);
    }

    /**
     * @param Request $request
     * @param array $parameters
     *
     * @return false|string
     */
    public function getAction(Request $request, array $parameters) {
        $ping = Ping::find($parameters["id"]);

        return json_encode($ping);
    }

    /**
     * @param Request $request
     * @param array $parameters
     *
     * @return false|string
     */
    public function createAction(Request $request, array $parameters) {
        $server = Ping::create($request->get());

        return json_encode($server);
    }
}

<?php

namespace Controllers;

use Models\Server;
use Services\Router\Request;

class ServerController
{
    /**
     * @return false|string
     */
    public function listAction() {
        $servers = Server::list();

        return json_encode($servers);
    }

    /**
     * @param Request $request
     * @param array $parameters
     *
     * @return false|string
     */
    public function getAction(Request $request, array $parameters) {
        $server = Server::find($parameters["id"]);

        return json_encode($server);
    }

    /**
     * @param Request $request
     * @param array $parameters
     *
     * @return false|string
     */
    public function updateAction(Request $request, array $parameters) {
        $server = Server::find($parameters["id"]);

        $server->update($request->get());

        return json_encode($server);
    }

    /**
     * @param Request $request
     * @param array $parameters
     *
     * @return false|string
     */
    public function createAction(Request $request, array $parameters) {
        $server = Server::create($request->get());

        return json_encode($server);
    }
}

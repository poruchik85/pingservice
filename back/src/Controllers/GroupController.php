<?php

namespace Controllers;

use Models\Group;
use Services\Router\Request;

class GroupController
{
    /**
     * @return false|string
     */
    public function listAction() {
        $groups = Group::list();

        return json_encode($groups);
    }

    /**
     * @param Request $request
     * @param array $parameters
     *
     * @return false|string
     */
    public function getAction(Request $request, array $parameters) {
        $group = Group::find($parameters["id"]);

        return json_encode($group);
    }

    /**
     * @param Request $request
     * @param array $parameters
     *
     * @return false|string
     */
    public function saveAction(Request $request, array $parameters) {
        $group = Group::find($parameters["id"]);

        $group->update($request->get());

        return json_encode($group);
    }
}

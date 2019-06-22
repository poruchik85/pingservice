<?php

use Services\Router\Request;
use Services\Router\Router;

$router = new Router(new Request);

$router->get("/group/list", "Controllers\GroupController@listAction");
$router->get("/group/{id}", "Controllers\GroupController@getAction");
$router->post("/group/{id}", "Controllers\GroupController@saveAction");
$router->post("/group", "Controllers\GroupController@createAction");
<?php

use Services\Router\Request;
use Services\Router\Router;

$router = new Router(new Request);

$router->get("/group/list", "Controllers\GroupController@listAction");
$router->get("/group/{id}", "Controllers\GroupController@getAction");
$router->post("/group/{id}", "Controllers\GroupController@updateAction");
$router->post("/group", "Controllers\GroupController@createAction");
$router->delete("/group/{id}", "Controllers\GroupController@deleteAction");

$router->get("/server/list", "Controllers\ServerController@listAction");
$router->get("/server/{id}", "Controllers\ServerController@getAction");
$router->post("/server/{id}", "Controllers\ServerController@updateAction");
$router->post("/server", "Controllers\ServerController@createAction");
$router->delete("/server/{id}", "Controllers\ServerController@deleteAction");

$router->get("/ping/list", "Controllers\PingController@listAction");
$router->get("/ping/{id}", "Controllers\PingController@getAction");
$router->post("/ping", "Controllers\PingController@createAction");

$router->get("/dashboard", "Controllers\DashboardController@indexAction");
$router->post("/dashboard/ping/{id}", "Controllers\DashboardController@pingAction");
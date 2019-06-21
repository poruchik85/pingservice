<?php

namespace PingService;

use JsonException;
use Models\Group;
use Models\Model;

require __DIR__.'/autoload.php';

try {
    Model::initStatic();
} catch (JsonException $e) {
    echo ("Config error");
}

/** @var Group $group */
$group = Group::find(1);

$group->name = "g123";

$group->save();
///** @var Group $group */
//$group = Group::find(1);
//
//var_dump($group->name);

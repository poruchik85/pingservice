<?php

namespace PingService;

use JsonException;
use Models\Model;

require __DIR__.'/autoload.php';
require __DIR__.'/routes.php';

try {
    Model::initStatic();
} catch (JsonException $e) {
    echo ("Config error");
}

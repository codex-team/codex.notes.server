<?php

namespace App;

use RKA\Middleware\IpAddress;
use App\System\Log;

require '../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => ['displayErrorDetails' => true]
]);

$app->add(new IpAddress());

require 'routes.php';

$app->run();
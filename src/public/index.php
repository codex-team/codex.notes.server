<?php

require '../../vendor/autoload.php';
require '../config/autoload.php';

require '../models/User.php';

$app = new \Slim\App([
    'settings' => ['displayErrorDetails' => true]
]);

$app->add(new RKA\Middleware\IpAddress());
    
require 'routes.php';

$app->run();
<?php

namespace App;

use \RKA\Middleware\IpAddress;
use \App\Modules\Log;

require '../../vendor/autoload.php';

/**
 *  Load APP configs
 *  
 *  Structure
 *  - $config['module']['setting'] = value;
 *  Use
 *  - global $config
 */
require '../config/autoload.php';

/**
 *  Load APP output messages
 *  
 *  Structure
 *  - $messages['module']['setting'] = value;
 *  Use
 *  - global $messages
 */
require '../messages/autoload.php';

$app = new \Slim\App([
    'settings' => ['displayErrorDetails' => true]
]);

$app->add(new IpAddress());

$logger = new Log();
    
require 'routes.php';

$app->run();
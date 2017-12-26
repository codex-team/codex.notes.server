<?php

namespace App;

use App\Components\Base\Models\Handlers\{
    AppExceptionHandler,
    CodeExceptionHandler,
    RouteExceptionHandler,
    MethodNotAllowedExceptionHandler
};
use App\System\Config;

define('PROJECTROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

/**
 * Autoload vendor
 */
require PROJECTROOT . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


/**
 * Load Dotenv
 * @see https://github.com/vlucas/phpdotenv
 */
if (is_file(PROJECTROOT . '.env')) {
    $dotenv = new \Dotenv\Dotenv(PROJECTROOT);
    $dotenv->load();
}

/**
 * Initialize App
 * @see \Slim\Container::$defaultSettings
 */
$app = new \Slim\App([
    'settings' => ['displayErrorDetails' => true]
]);

$c = $app->getContainer();


$c['errorHandler'] = function ($c) {
    return new AppExceptionHandler();
};

$c['notFoundHandler'] = function ($c) {
    return new RouteExceptionHandler();
};

$c['phpErrorHandler'] = function ($c) {
    return new CodeExceptionHandler();
};

$c['notAllowedHandler'] = function ($c) {
    return new MethodNotAllowedExceptionHandler();
};

/**
 * Enable modules
 */
require PROJECTROOT . Config::DIR_PUBLIC . DIRECTORY_SEPARATOR . 'modules.php';

/**
 * Set routes
 */
require PROJECTROOT . Config::DIR_PUBLIC . DIRECTORY_SEPARATOR . 'routes.php';

/**
 * Run App
 */
$app->run();
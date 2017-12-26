<?php

namespace App;

use App\Components\Base\Models\Handlers\AppExceptionHandler;
use App\Components\Base\Models\Handlers\CodeExceptionHandler;
use App\Components\Base\Models\Handlers\RouteExceptionHandler;
use App\Components\Base\Models\Handlers\MethodNotAllowedExceptionHandler;

define('PROJECTROOT', realpath(dirname(__FILE__)) . '/../');

/**
 * Autoload vendor
 */
require PROJECTROOT . 'vendor/autoload.php';

/**
 * Load Dotenv
 * @see https://github.com/vlucas/phpdotenv
 */
if (is_file(PROJECTROOT . '.env')) {
    $dotenv = new \Dotenv\Dotenv(PROJECTROOT);
    $dotenv->load();
}

/**
 * Init App
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
require PROJECTROOT . 'public/modules.php';

/**
 * Set routes
 */
require PROJECTROOT . 'public/routes.php';

/**
 * Run App
 */
$app->run();
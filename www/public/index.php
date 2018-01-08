<?php

namespace App;

use App\Components\Base\Models\Handlers\{
    AppExceptionHandler,
    CodeExceptionHandler
};
use App\System\Config;

define('PROJECTROOT', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);

/**
 * Custom autoloader
 */
require PROJECTROOT . 'components/autoload.php';

/**
 * Autoload vendor
 */
require PROJECTROOT . Config::DIR_VENDOR . DIRECTORY_SEPARATOR . 'autoload.php';


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

$c['phpErrorHandler'] = function ($c) {
    return new CodeExceptionHandler();
};

/**
 * Enable modules
 */
require PROJECTROOT . Config::DIR_COMPONENTS . DIRECTORY_SEPARATOR . 'modules.php';

/**
 * Set routes
 */
require PROJECTROOT . Config::DIR_COMPONENTS . DIRECTORY_SEPARATOR . 'routes.php';

/**
 * Run App
 */
$app->run();

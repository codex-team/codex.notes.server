<?php

namespace App;

use App\Components\Base\Models\BaseException;
use App\Components\Base\Models\Handlers\{
    AppExceptionHandler,
    CodeExceptionHandler
};
use App\System\Config;

define('PROJECTROOT', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);

/**
 * Custom autoloader
 */
require 'autoload.php';

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
$app = new \Slim\App();
$c = $app->getContainer();

/**
 * Catch exceptions
 */
$c['errorHandler'] = function ($c) {
    return new AppExceptionHandler();
};

/**
 * Catch fatals
 */
$c['phpErrorHandler'] = function ($c) {
    return new CodeExceptionHandler();
};

/**
 * Catch notices and warnings
 */
set_error_handler(function ($severity, $message) {
    throw new BaseException($message, $severity);
}, E_ALL);

/**
 * Set 404 handler
 */
$c['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Page not found');
    };
};

/**
 * Enable Hawk Catcher
 */
\Hawk\HawkCatcher::instance(Config::get('HAWK_TOKEN'));

/**
 * Enable modules
 */
require 'modules.php';

/**
 * Set routes
 */
require 'routes.php';

/**
 * Run App
 */
$app->run();

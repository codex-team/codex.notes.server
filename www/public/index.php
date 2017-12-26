<?php

namespace App;

define('PROJECTROOT', realpath(dirname(__FILE__ ).'/../').DIRECTORY_SEPARATOR);

/**
 * Autoload vendor
 */
require PROJECTROOT . 'vendor/autoload.php';

/**
 * Load Dotenv
 * @see https://github.com/vlucas/phpdotenv
 */
if (is_file('.env')) {
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

/**
 * Enable modules
 */
require PROJECTROOT.'public/modules.php';

/**
 * Set routes
 */
require PROJECTROOT.'public/routes.php';

/**
 * Run App
 */
$app->run();
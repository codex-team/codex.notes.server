<?php

namespace App;

define('DOCROOT', realpath(dirname(__FILE__ )).DIRECTORY_SEPARATOR);

/**
 * Autoload vendor
 */
require DOCROOT.'vendor/autoload.php';

/**
 * Load Dotenv
 * @see https://github.com/vlucas/phpdotenv
 */
if (is_file('.env')) {
    $dotenv = new \Dotenv\Dotenv(DOCROOT);
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
require DOCROOT.'app/modules.php';

/**
 * Set routes
 */
require DOCROOT.'app/routes.php';

/**
 * Run App
 */
$app->run();
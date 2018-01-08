<?php

namespace Tests;

//use Tests\Helpers\WebTestCase;
use There4\Slim\Test\WebTestCase;

define('PROJECTROOT', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);

/**
 * Enable autoloaders
 */
include_once PROJECTROOT . "components/autoload.php";
include_once PROJECTROOT . "vendor/autoload.php";

/**
 * Class LocalWebTestCase
 * @package Tests
 *
 * Class for performing application testing with HTTP Requests
 */
class LocalWebTestCase extends WebTestCase {

    /**
     * Register application instance for testing
     */
    public function getSlimInstance() {
        $app = new \Slim\App([
            'settings' => ['displayErrorDetails' => false]
        ]);

        /**
         * Load Dotenv
         * @see https://github.com/vlucas/phpdotenv
         */
        if (is_file(PROJECTROOT . '.env')) {
            $dotenv = new \Dotenv\Dotenv(PROJECTROOT);
            $dotenv->load();
        }

        /**
         * Enable modules
         */
        include PROJECTROOT . 'components/modules.php';

        /**
         * Set routes
         */
        include PROJECTROOT . 'components/routes.php';

        return $app;
    }
};


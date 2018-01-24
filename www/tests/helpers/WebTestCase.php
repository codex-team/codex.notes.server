<?php

namespace App\Tests\Helpers;

/**
 * Class WebTestCase
 *
 * @package App\Tests\Helpers
 *
 * Class for performing application testing with HTTP Requests
 */
class WebTestCase extends \PHPUnit\Framework\TestCase
{
    /** @var \Slim\App */
    protected $app;
    /** @var WebTestClient */
    protected $client;

    public function setup()
    {
        // Establish a local reference to the Slim app object
        // Ensure no cache Router
        $this->app = $this->getSlimInstance();
        $this->client = new WebTestClient($this->app);
    }

    /**
     * Register application instance for testing
     */
    public function getSlimInstance()
    {
        $app = new \Slim\App();

        $this->loadEnvironment();

        /**
         * Enable modules
         */
        include PROJECTROOT . 'public/modules.php';

        /**
         * Set routes
         */
        include PROJECTROOT . 'public/routes.php';

        return $app;
    }

    /**
     * Load environment variables
     */
    public function loadEnvironment()
    {
        /**
         * Load Dotenv
         *
         * @see https://github.com/vlucas/phpdotenv
         */
        $env_path = PROJECTROOT . 'tests/helpers/';
        $env_name = '.env.test';

        if (is_file($env_path . $env_name)) {
            $dotenv = new \Dotenv\Dotenv($env_path, $env_name);
            $dotenv->load();
        }
    }
}

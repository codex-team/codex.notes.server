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

    /**
     * Send GraphQl request and check response structure
     *
     * @param string $type – query or migration
     * @param string $name – operation name equals to .graphql base filename
     * @param array  $data – array of variables
     *
     * @return array – response data
     */
    public function sendGraphql(string $type, string $name, array $data): array
    {
        $request = GraphQl::request($type, $name, $data);
        $output = $this->client->post('/graphql', $request);

        // check auth
        $this->assertFalse($this->client->response->isForbidden(), 'Auth Error (403).');

        // check json output structure
        $data = json_decode($output, true);
        $this->assertEquals(json_last_error(), JSON_ERROR_NONE);
        $this->assertArrayHasKey('data', $data);

        return $data['data'];
    }
}

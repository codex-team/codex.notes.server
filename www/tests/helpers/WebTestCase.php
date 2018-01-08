<?php

namespace Tests\Helpers;

use Tests\Helpers\WebTestClient;

abstract class WebTestCase extends \PHPUnit\Framework\TestCase
{
    /** @var \Slim\App */
    protected $app;
    /** @var WebTestClient */
    protected $client;

    /**
     * Run for each unit test to setup our slim app environment
     */
    public function setup()
    {
        // Establish a local reference to the Slim app object
        // Ensure no cache Router
        $this->app    = $this->getSlimInstance();
        $this->client = new WebTestClient($this->app);
    }

    /**
     * Abstract method to get slim instance
     */
    abstract protected function getSlimInstance();
}
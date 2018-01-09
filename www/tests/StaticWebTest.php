<?php

namespace App\Tests;

use App\Tests\Helpers\WebTestCase;

/**
 * Class StaticWebTest
 * @package App\Tests
 *
 * Test static routes
 */
class StaticWebTest extends WebTestCase
{
    /**
     * Test if main page is accessible via HTTP GET Request
     */
    public function testMainPageIsOk() {
        $this->client->get('/');
        $this->assertTrue($this->client->response->isOk());
    }

    /**
     * Test if main page has default test
     */
    public function testMainPageTextIsVisible() {
        $output = $this->client->get('/');
        $this->assertContains('<h1>CodeX Notes</h1>', $output);
    }

    /**
     * Test if undefined page is not defined
     */
    public function testPageNotFound() {
        $this->client->get('/unexisted/page');
        $this->assertTrue($this->client->response->isNotFound());
    }
}
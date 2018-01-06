<?php

namespace Tests;

require_once "WebTestCase.php";

class RestTest extends WebTestCase
{
    public function getSlimInstance() {
        require dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . "app/app.php";
        return $app;
    }

    public function testMainPageOk() {
        $this->client->get('/');
        $this->assertTrue($this->client->response->isOk());
    }

    public function testPageNotFound() {
        $this->client->get('/unexisting');
        $this->assertTrue($this->client->response->isNotFound());
    }
}
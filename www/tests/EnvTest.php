<?php

namespace Tests;

class EnvTest extends \PHPUnit\Framework\TestCase
{
    public function setup() {
        require dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . "app/app.php";
        $this->app = $app;
    }

    public function testEnvFileExists() {
        $this->assertTrue(is_file(PROJECTROOT . '.env'));
    }

    public function testEnvFileSaltChanged() {
        $this->assertNotEquals(getenv('INVITATION_SALT'), 'somesalt123');
    }
}
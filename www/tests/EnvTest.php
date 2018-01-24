<?php

namespace App\Tests;

/**
 * Class EnvTest
 *
 * @package App\Tests
 *
 * Test existence and validity of environment settings and .env file
 */
class EnvTest extends \PHPUnit\Framework\TestCase
{
    /**
     * File .env should exist
     */
    public function testEnvFileExists()
    {
        $this->assertTrue(is_file(PROJECTROOT . '.env'));
    }

    /**
     * Default salt value should be changed
     */
    public function testEnvFileSaltChanged()
    {
        $dotenv = new \Dotenv\Dotenv(PROJECTROOT);
        $dotenv->load();
        $this->assertNotEquals(getenv('INVITATION_SALT'), 'somesalt123');
    }
}

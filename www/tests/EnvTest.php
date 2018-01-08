<?php

namespace Tests;

/**
 * Class EnvTest
 * @package Tests
 *
 * Test existence and validity of environment settings and .env file
 */
class EnvTest extends \PHPUnit\Framework\TestCase
{
    /**
     * File .env should exist
     */
    public function testEnvFileExists() {
        $this->assertTrue(is_file(PROJECTROOT . '.env'));
    }

    /**
     * Default salt value should be changed
     */
    public function testEnvFileSaltChanged() {
        $this->assertNotEquals(getenv('INVITATION_SALT'), 'somesalt123');
    }

}
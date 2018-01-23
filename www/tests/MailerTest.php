<?php

namespace App\Tests;

use App\Components\Base\Models\Mailer;
use App\System\Config;

/**
 * Class MailerTest
 *
 * @package App\Tests
 *
 * Test validity of Mailer class
 */
class MailerTest extends \PHPUnit\Framework\TestCase
{
    // If need to skip remote tests
    private $skipRemote = false;

    /**
     * Load environment variables
     */
    public function setup()
    {
        $env_path = PROJECTROOT . 'tests/helpers/';
        $env_name = '.env.test';

        if (is_file($env_path . $env_name)) {
            $dotenv = new \Dotenv\Dotenv($env_path, $env_name);
            $dotenv->overload();
        }

        if (empty(Config::get('MAILER_SERVER'))) {
            $this->markTestSkipped('MAILER_SERVER is not set. Skipped.');
        }
    }

    /**
     * Test if mail can be successfully sent
     */
    public function testEmailSend()
    {
        $mailer = Mailer::instance();
        $result = $mailer->send("subject", "3285b08cb2-87bb61@inbox.mailtrap.io", "3285b08cb2-87bb61@inbox.mailtrap.io", "hello");
        $this->assertTrue($result);
    }
}

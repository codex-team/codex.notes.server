<?php

namespace App\Components\Base\Models;

use \Swift_SmtpTransport;
use \Swift_Message;
use \Swift_Mailer;

/**
 * Class Mailer
 * @package App\Components\Base\Models
 */
class Mailer
{

    private static $_instance;
    private static $_connection;

    public static function instance() {

        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        if (!isset(self::$_connection)) {

            // Create the Transport
            $transport = (new Swift_SmtpTransport('smtp.example.org', 25))
                ->setUsername('your username')
                ->setPassword('your password')
            ;

            // Create the Mailer using your created Transport
            self::$_connection = new Swift_Mailer($transport);
        }

        return self::$_instance;
    }

    /**
     * @param string $subject
     * @param array $sendFrom
     * @param array $receivers
     * @param array $headers
     * @param array $attachments
     */
    public function send(string $subject, array $sendFrom, array $recipients, $body, array $headers = [], array $attachments = []) {

        // Create a message
        $message = (new Swift_Message($subject));

        // Configure senders
        if (!empty($sendFrom)) {
            foreach($sendFrom as $address => $name) {
                if (is_int($address)) {
                    $message->setFrom($name);
                } else {
                    $message->setFrom([$address => $name]);
                }
            }
        }

        // Configure recipients
        if (!empty($recipients)) {
            foreach($recipients as $address => $name) {
                if (is_int($address)) {
                    $message->setTo($name);
                } else {
                    $message->setTo([$address => $name]);
                }
            }
        }

        // Body can contain content-type
        if (is_array($body)) {
            $message->setBody($body['text'], $body['content-type']);
        } else {
            $message->setBody($body);
        }

        if (!empty($headers)) {
            $headers = $message->getHeaders();
            foreach($headers as $header => $value) {
                if (!is_int($header)) {
                    $headers->addTextHeader($header, $value);
                }
            }
        }

    }

    private function __construct() { }

    private function __sleep() { }

    private function __clone() { }
}
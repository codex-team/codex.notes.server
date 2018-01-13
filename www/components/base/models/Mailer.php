<?php

namespace App\Components\Base\Models;

use \Swift_SmtpTransport;
use \Swift_Message;
use \Swift_Mailer;
use \Swift_Attachment;
use App\System\Config;

/**
 * Class Mailer
 * @package App\Components\Base\Models
 */
class Mailer
{

    const LOCALHOST = 'localhost';
    const SMTP_PORT = 25;

    /**
     * @var $_instance
     * Instance holder
     */
    private static $_instance;

    /**
     * @var null|Swift_Mailer
     */
    private $mailer = null;

    public static function instance()
    {

        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Method that send sends a message
     *
     * @param string $subject - title of message
     * @param array $sendFrom - list of senders email or name
     * @param array|string $body - message body with content-type
     * @param array $recipients - list of recipients emails
     * @param array $headers - specific headers. Rarely used
     * @param array $attachments - message atthachments
     *
     * @return {boolean}
     */
    public function send(string $subject, array $sendFrom, array $recipients, $body, array $headers = [], array $attachments = [])
    {

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

        // If message has specific headers
        if (!empty($headers)) {
            $headers = $message->getHeaders();
            foreach($headers as $header => $value) {
                if (!is_int($header)) {
                    $headers->addTextHeader($header, $value);
                }
            }
        }

        if (!empty($attachments)) {
            foreach($attachments as $file => $contentType) {
                if (is_int($file)) {
                    $attachment = Swift_Attachment::fromPath($file, $contentType);
                } else {
                    // send attachment without content-type
                    $attachment = Swift_Attachment::fromPath($contentType);
                }

                $message->attach($attachment);
            }
        }

        return $this->mailer->send($message);
    }

    private function __construct()
    {

        $server = Config::get('MAILER_SERVER') ?? self::LOCALHOST;
        $port = Config::get('MAILER_POST') ?? self::SMTP_PORT; // local sendmail port

        $username = Config::get('MAILER_USERNAME') ?? 'admin';
        $password = Config::get('MAILER_PASSWORD') ?? 'admin';

        // Create the Transport
        $transport = (new Swift_SmtpTransport($server, $port))
            ->setUsername($username)
            ->setPassword($password);

        // Create the Mailer using your created Transport
        $this->mailer = new Swift_Mailer($transport);

    }

    private function __sleep() { }

    private function __clone() { }
}
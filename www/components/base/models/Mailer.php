<?php

namespace App\Components\Base\Models;

use App\Components\Base\Models\Exceptions\MailerException;
use \Swift_SmtpTransport;
use \Swift_Message;
use \Swift_Mailer;
use \Swift_Attachment;
use App\System\Config;

/**
 * Class Mailer (Singleton)
 * @package App\Components\Base\Models
 */
class Mailer
{
    const DEFAULT_HOST = 'localhost';
    const DEFAULT_SMTP_PORT = 25;

    /**
     * @var $_instance
     * Instance holder
     */
    private static $instance;

    /**
     * @var null|Swift_Mailer
     */
    private $mailer = null;

    /**
     * Return Mailer instance. Support singleton.
     *
     * @return Mailer
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Send a message
     *
     * @param string $subject - message title
     * @param string $sendFrom - list of senders email or name
     * @param array|string $body - message body with content-type
     * @param array|string $recipients - list of recipients emails or single email address
     * @param array $headers - specific headers. Rarely used
     * @param array $attachments - message attachments
     *
     * @return bool
     */
    public function send(string $subject, string $sendFrom, $recipients, $body, array $headers = [], array $attachments = []): bool
    {
        // Create a new message
        $message = (new Swift_Message($subject));

        // Check inputs
        if (empty($sendFrom)) {

            throw new MailerException('Message sendFrom list is empty');

        }

        if (empty($recipients)) {

            throw new MailerException('Message recipients list is empty');

        }

        // Configure sender
        if (is_string($sendFrom)) {

            $message->setFrom($sendFrom);

        } else {

            throw new MailerException('Message sendFrom argument should be a String');

        }


        // Configure recipients
        if (is_string($recipients)) {

            $message->setTo($recipients);

        } elseif (is_array($recipients)) {

            foreach($recipients as $address => $name) {

                $message->setTo([$address => $name]);

            }

        } else {

            throw new MailerException('Message recipients argument should be String or Array');

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

                if (is_string($header)) {

                    $headers->addTextHeader($header, $value);

                }

            }
        }

        if (!empty($attachments)) {

            foreach($attachments as $key => $payload) {

                // key is a filename and payload is content-type
                if (is_string($key)) {

                    $attachment = Swift_Attachment::fromPath($key, $payload);

                } else {

                    // payload is a filename
                    $attachment = Swift_Attachment::fromPath($payload);

                }

                $message->attach($attachment);
            }
        }

        return $this->mailer->send($message);
    }

    private function __construct()
    {
        $server = Config::get('MAILER_SERVER') ?? self::DEFAULT_HOST;
        $port = Config::get('MAILER_PORT') ?? self::DEFAULT_SMTP_PORT;

        $username = Config::get('MAILER_USERNAME') ?? '';
        $password = Config::get('MAILER_PASSWORD') ?? '';

        // Create the Transport
        $transport = new Swift_SmtpTransport($server, $port);

        if (!empty($username) && !empty($password)) {

            $transport->setUsername($username)->setPassword($password);

        }

        // Create the Mailer using your created Transport
        $this->mailer = new Swift_Mailer($transport);

    }

    private function __sleep() { }

    private function __clone() { }
}
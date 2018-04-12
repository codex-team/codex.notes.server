<?php

namespace App\Components\Notify;

use App\Components\Sockets\Sockets;

class Notify
{
    const FOLDER_UPDATE = 'folder updated';
    const NOTE_UPDATE = 'note updated';
    const COLLABORATOR_INVITE = 'collaborator invited';
    const COLLABORATOR_JOIN = 'collaborator joined';

    /**
     * Send event with payload to target channel
     *
     * @param string $channel
     * @param string $event
     * @param        $data
     * @param        $sender
     */
    public static function send(string $channel, string $event, $data, $sender): void
    {
        $message = [
            'event' => $event,
            'data' => $data,
            'sender' => $sender
        ];

        Sockets::push($channel, $message);
    }
}

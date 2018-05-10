<?php

namespace App\Components\Notify;

use App\Components\Api\Models\User;
use App\Components\Sockets\Sockets;

class Notify
{
    const FOLDER_RENAME = 'folder renamed';
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
     * @param User   $sender
     */
    public static function send(string $channel, string $event, $data, User $sender): void
    {
        $message = [
            'event' => $event,
            'data' => $data,
            'sender' => $sender
        ];

        Sockets::push($channel, $message);
    }
}

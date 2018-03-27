<?php

namespace App\Components\Sockets;

use App\System\Config;
use App\System\Log;

class Pusher
{
    const TYPE_FOLDER = 'folder';
    const TYPE_NOTE = 'note';
    const TYPE_COLLABORATOR = 'collaborator';

    const EVENT_CREATE = 'create';
    const EVENT_UPDATE = 'update';

    public static function send(string $channel, string $type, string $event, $data)
    {
        $message = [
            'type' => $type,
            'event' => $event,
            'data' => $data
        ];

//        Log::instance()->debug("[PUSHER] channel:{$channel} <- data:" . json_encode($message));

        Sockets::push($channel, $message);
    }
}

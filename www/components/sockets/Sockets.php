<?php

namespace App\Components\Sockets;

use App\System\Config;
use App\System\Log;

class Sockets
{
    public static function push($channel, $message)
    {
        Log::instance()->debug('channel: ' . $channel . ', message: ' . $message);

        $ch = curl_init(Config::get('SOCKETS_HOST') . 'chan/' . $channel);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        curl_exec($ch);
        curl_close($ch);
    }
}

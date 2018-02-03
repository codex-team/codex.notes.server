<?php

namespace App\Components\Sockets;


use App\System\Config;

class Sockets
{
    public static function push($channel, $message) {
        $ch = curl_init(Config::get('SERVER_URI') . 'chan/'. $channel . '.b10');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

        curl_close($ch);
    }

}

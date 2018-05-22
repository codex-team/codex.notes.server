<?php

namespace App\Components\Sockets;

use App\System\Config;
use App\System\Log;

class Sockets
{
    /**
     * Push any type of data to target channel
     * Client will receive json encoded data in format: {'message': $message}
     *
     * @param string $channel
     * @param        $message
     */
    public static function push(string $channel, $message): void
    {
        $deviceId = null;

        try {
            $deviceId = $GLOBALS['device-id'];
        } catch (\Exception $e) {
            Log::instance()->alert('Cannot $deviceId from $GLOBALS');
        }

        $data = [
            'message' => $message,
            'device-id' => $deviceId
        ];

        /**
         * Encoded data to string
         *
         * @var string
         */
        $encodedData = json_encode($data);

//        $ch = curl_init(self::createChannelUrl($channel));
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
//        curl_exec($ch);
//        curl_close($ch);
    }

    /**
     * Compose full path to channel
     *
     * @param string $channel
     *
     * @return string
     */
    public static function createChannelUrl($channel): string
    {
        return Config::get('SOCKETS_HOST') . 'chan/' . $channel;
    }
}

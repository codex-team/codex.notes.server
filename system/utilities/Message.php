<?php

namespace App\System\Utilities;

class Message extends Base
{
    public static function load(string $apiVer = 'v1', string $message = '')
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/../'
            . self::DIR_VERSIONS . '/'
            . $apiVer . '/'
            . self::DIR_MESSAGES . '/'
            . $message . '.php';

        $content = file_get_contents($path);
        $content = preg_replace('/<\?php/', '', $content);

        return eval($content);
    }
}
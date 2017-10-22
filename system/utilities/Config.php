<?php

namespace App\System\Utilities;

class Config extends Base
{
    public static function load(string $config = '')
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/../'
                    . self::DIR_CONFIG . '/'
                    . $config . '.php';

        $content = file_get_contents($path);
        $content = preg_replace('/<\?php/', '', $content);

        return eval($content);
    }

    public static function getPathTo(string $folder)
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/../' . $folder;
    }
}
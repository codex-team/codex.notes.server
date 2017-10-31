<?php

namespace App\System\Utilities;

/**
 * Class Message
 * Создан для работы с сообщениями
 *
 * @package App\System\Utilities
 */
class Messages extends Base
{
    /**
     * Возвращает ассоциативный массив сообщений
     * @param string $apiVer    В зависимости от версии API тексты могут отличаться
     * @param string $message   Имя файла с сообщениями
     * @return array
     */
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
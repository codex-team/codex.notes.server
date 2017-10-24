<?php

namespace App\System\Utilities;

/**
 * Class Config
 * Класс работает с файлами настроек прилодения
 *
 * @package App\System\Utilities
 */
class Config extends Base
{
    /**
     * Метод реализует подгрузку параметров (массива) из .php файла
     * @param string $config    Имя файла настроек
     * @return array
     */
    public static function load(string $config = '')
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/../'
                    . self::DIR_CONFIG . '/'
                    . $config . '.php';

        $content = file_get_contents($path);
        $content = preg_replace('/<\?php/', '', $content);

        return eval($content);
    }

    /**
     * Возвращает абсолютный путь к нужной нам папке
     * @param string $folder
     * @return string
     */
    public static function getPathTo(string $folder)
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/../' . $folder;
    }
}
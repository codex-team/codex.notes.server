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
     * @deprecated Use .env file
     *
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
    public static function getPathTo(string $folder): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/../' . $folder;
    }

    /**
     * Return an absolute path to the base dir
     * @return string
     */
    public static function baseDir(): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/../';
    }

    /**
     * Return true if debug flag enabled in the .env config file
     * @return bool
     */
    public static function debug(): bool
    {
        return !empty($_SERVER['DEBUG']) && $_SERVER['DEBUG'] == 'TRUE';
    }
}
<?php

namespace App\System;

/**
 * Class Config
 *
 * @package App\System\Utilities
 */
class Config extends Base
{
    /**
     * Return value from .env or null
     *
     * @param string $param
     * @return string
     */
    public static function get(string $param)
    {
        return $_SERVER[$param] ?? null;
    }


    /**
     * Return true if debug flag enabled in the .env config file
     *
     * @return bool
     */
    public static function debug(): bool
    {
        return (boolean) self::get('debug');
    }
}
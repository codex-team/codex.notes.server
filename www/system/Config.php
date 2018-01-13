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
    public static function get(string $param): ?string
    {
        return $_SERVER[$param] ?? null;
    }

    /**
     * Return boolean value for param from .env
     *
     * @param string $param
     * @return bool
     */
    public static function getBool(string $param): ?bool
    {
        $value = filter_var(self::get($param), FILTER_VALIDATE_BOOLEAN);

        return $value;
    }

    /**
     * Return true if debug flag enabled in the .env config file
     *
     * @return bool
     */
    public static function debug(): bool
    {
        return self::getBool('DEBUG');
    }
}
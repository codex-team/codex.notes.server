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
     * Return true if debug flag enabled in the .env config file
     * @return bool
     */
    public static function debug(): bool
    {
        return $_SERVER['DEBUG'] ? $_SERVER['DEBUG'] == True : False;
    }
}
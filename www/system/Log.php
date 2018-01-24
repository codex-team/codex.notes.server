<?php

namespace App\System;

use Katzgrau\KLogger\Logger;

/**
 * Class Log
 *
 * Прослойка для стороннего модуля логирования
 *
 * @package App\System
 */
class Log
{
    private static $instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Return Logger instance
     *
     * @param string|null $logDir
     *
     * @return Logger|null
     */
    public static function instance(string $logDir = null)
    {
        if (is_null(self::$instance) || !is_null($logDir)) {
            $logDir = $logDir ?: Config::DIR_LOGS;

            $path = PROJECTROOT . $logDir;

            self::$instance = new Logger($path);
        }

        return self::$instance;
    }
}

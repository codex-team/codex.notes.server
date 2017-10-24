<?php

namespace App\System;

use App\System\Utilities\Config;
use Katzgrau\KLogger\Logger;

/**
 * Class Log
 *
 * Прослойка для стороннего модуля логирования
 * @package App\System
 */
class Log extends Logger
{
    /**
     * Инициализируем логгер с помощью указания пути к папке с логами
     * @param string $logDir
     */
    function __construct(string $logDir = '')
    {
        if ($logDir) {
            $path = $logDir;
        }
        else {
            $path = Config::getPathTo(Config::DIR_LOGS);
        }

        parent::__construct($path);
    }
}
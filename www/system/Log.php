<?php

namespace App\System;

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
        $logDir = $logDir ?: Base::LOGS;

        $path = PROJECTROOT . $logDir;

        parent::__construct($path);
    }
}
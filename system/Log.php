<?php

namespace App\System;

use App\System\Utilities\Config;
use Katzgrau\KLogger\Logger;

/**
 * @method info  (string $text)
 * @method error (string $text)
 * @method debug (string $text, array $params)
 */
class Log extends Logger
{    
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
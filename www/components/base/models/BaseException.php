<?php

namespace App\Components\Base\Models;

use App\System\Log;
use Exception;

class BaseException extends Exception
{
    public function __construct($message, $code = 0, $previous = null)
    {
        Log::instance()->warning($message);

        parent::__construct($message, $code, $previous);
    }
}

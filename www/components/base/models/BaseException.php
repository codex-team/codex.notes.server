<?php

namespace App\Components\Base\Models;

use Exception;
use App\System\Log;

class BaseException extends Exception
{
    public function __construct($message, $code = 0, $previous = null)
    {
        Log::instance()->debug($message);

        parent::__construct($message, $code, $previous);
    }
}
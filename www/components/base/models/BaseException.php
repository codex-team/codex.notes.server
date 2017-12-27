<?php

namespace App\Components\Base\Models;

use Exception;
use App\System\Log;

class BaseException extends Exception {

    protected $logger;

    public function __construct($message, $code = 0, $previous = null)
    {
        $this->logger = new Log();

        $this->logger->warning($message);

        parent::__construct($message, $code, $previous);
    }
}
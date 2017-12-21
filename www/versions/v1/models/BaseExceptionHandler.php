<?php

namespace App\Versions\V1\Models;

use App\System\Log;

class BaseExceptionHandler {

    protected $logger;

    public function __construct()
    {
        if (!$this->logger) {
            $this->logger = new Log();
        }
    }
}
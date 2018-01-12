<?php

namespace App\Components\Base\Models;

use App\System\Log;

class BaseExceptionHandler {

    protected $logger;

    public function __construct()
    {
        if (!$this->logger) {
            $this->logger = Log::instance();
        }
    }
}
<?php

namespace App\Versions\V1\Models;

use App\System\Log;

class Base {

    protected $logger;

    protected $config;

    protected $messages;

    public function __construct()
    {
        if (!$this->logger) {
            $this->logger = new Log();
        }
    }
}
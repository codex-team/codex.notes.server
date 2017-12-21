<?php

namespace App\Versions\V1\Controllers;

use App\System\Log;

/**
 * Class Base
 * Родитель для остальных контроллеров
 *
 * @package App\Versions\V1\Controllers
 */
class Base {

    protected $logger;

    public function __construct() {
        if (!$this->logger) {
            $this->logger = new Log();
        }
    }
}
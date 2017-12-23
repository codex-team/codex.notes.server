<?php

namespace App\Components\Base\Models;

use Exception;

class BaseException extends Exception {

    public function __construct($message, $code = 0, $previous = null) {

        parent::__construct($message, $code, $previous);
    }
}
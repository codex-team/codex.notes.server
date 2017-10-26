<?php

namespace App\Versions\V1\Models\Exceptions;

use App\Versions\V1\Models\BaseException;

class ApiException extends BaseException {

    public function __construct($message, $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
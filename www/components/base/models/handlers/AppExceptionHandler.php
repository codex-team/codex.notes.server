<?php

namespace App\Components\Base\Models\Handlers;

use App\Components\Base\Models\BaseExceptionHandler;

class AppExceptionHandler extends BaseExceptionHandler {

    public function __construct()
    {
        parent::__construct();
    }

    public function __invoke($request, $response, $exception)
    {
        $message = $exception->getMessage() . ' in ' . $exception->getFile() . ' : ' . $exception->getLine();

        $this->logger->notice($message);
    }
}
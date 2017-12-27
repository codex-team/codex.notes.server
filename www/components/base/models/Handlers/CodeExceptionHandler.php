<?php

namespace App\Components\Base\Models\Handlers;

use App\Components\Base\Models\BaseExceptionHandler;
use App\System\Config;

class CodeExceptionHandler extends BaseExceptionHandler
{

    public function __construct()
    {
        parent::__construct();
    }

    public function __invoke($request, $response, $exception)
    {
        $message = $exception->getMessage() . ' in ' . $exception->getFile() . ' : ' . $exception->getLine();

        $message = Config::debug() ? $message : "Internal Server Error";

        $this->logger->emergency($message);

        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write($message);
    }
}
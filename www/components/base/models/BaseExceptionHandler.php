<?php

namespace App\Components\Base\Models;

use App\System\{
    Config, Http, Log
};

class BaseExceptionHandler
{
    public function __construct() {}

    public function __invoke($request, $response, $exception)
    {

        /**
         * Log exception
         */
        $logMessage = sprintf(
            "%s in %s:%s\n%s",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        Log::instance()->debug($logMessage);

        /**
         * Return message to user
         */
        $showMessage = !Config::debug() ? HTTP::STRING_SERVER_ERROR : sprintf(
            "%s in %s:%s",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write($showMessage);
    }
}
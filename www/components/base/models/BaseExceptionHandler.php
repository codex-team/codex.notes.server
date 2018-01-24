<?php

namespace App\Components\Base\Models;

use App\System\{
    Config,
    HTTP,
    Log
};
use Hawk\HawkCatcher;

class BaseExceptionHandler
{
    public function __construct()
    {
    }

    public function __invoke($request, $response, $exception)
    {
        HawkCatcher::catchException($exception);

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

        Log::instance()->error($logMessage);

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

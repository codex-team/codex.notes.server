<?php

namespace App\Components\Base\Models\Handlers;

use App\Components\Base\Models\BaseExceptionHandler;
use App\System\Config;
use App\System\HTTP;

class CodeExceptionHandler extends BaseExceptionHandler
{

    public function __construct()
    {
        parent::__construct();
    }

    public function __invoke($request, $response, $exception)
    {
        $message = $exception->getMessage() . ' in ' . $exception->getFile() . ' : ' . $exception->getLine();

        $message = Config::debug() ? $message : HTTP::STRING_SERVER_ERROR;

        $this->logger->emergency($message);

        return $response
            ->withStatus(HTTP::CODE_SERVER_ERROR)
            ->withHeader('Content-Type', 'text/html')
            ->write($message);
    }
}
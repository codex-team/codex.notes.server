<?php

namespace App\Versions\V1\Models\Handlers;

use App\Versions\V1\Models\BaseExceptionHandler;
use App\Versions\V1\Api;
use App\System\HTTP;

class CodeExceptionHandler extends BaseExceptionHandler
{

    public function __construct()
    {
        parent::__construct();
    }

    public function __invoke($request, $response, $exception) {

        $api = new Api();
        $_response = $api->getDefaultResponseAsArray();

        $_response['code'] = HTTP::CODE_SERVER_ERROR;
        $_response['result'] = HTTP::STRING_SERVER_ERROR;
        $_response['success'] = false;

        $this->logger->emergency($exception->getMessage());

        return $response->withJson(
            $_response,
            $_response['code']
        );

    }
}
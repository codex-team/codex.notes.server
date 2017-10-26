<?php

namespace App\Versions\V1\Models\Handlers;

use App\Versions\V1\Models\BaseExceptionHandler;
use App\Versions\V1\Api;

class AppExceptionHandler extends BaseExceptionHandler {

    public function __construct()
    {
        parent::__construct();
    }

    public function __invoke($request, $response, $exception) {

        $api = new Api();
        $_response = $api->getDefaultResponseAsArray();

        $_response['code'] = $exception->getCode();
        $_response['result'] = $exception->getMessage();
        $_response['success'] = false;

        $this->logger->notice($_response['result']);

        return $response->withJson(
            $_response,
            $_response['code']
        );

    }
}
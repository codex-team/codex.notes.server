<?php

namespace App\Components\Base\Models\Handlers;

use App\Components\Base\Models\BaseExceptionHandler;
//use App\Versions\V1\Api;
//use App\System\HTTP;

class MethodNotAllowedExceptionHandler extends BaseExceptionHandler
{

    public function __construct()
    {
        parent::__construct();
    }

//    public function __invoke($request, $response, $methods) {
//
//        $api = new Api();
//        $_response = $api->getDefaultResponseAsArray();
//
//        $message = sprintf(HTTP::STRING_NOT_ALLOWED_METHOD, implode(', ', $methods));
//
//        $_response['code'] = HTTP::CODE_NOT_ALLOWED_METHOD;
//        $_response['result'] = $message;
//        $_response['success'] = false;
//
//        $this->logger->emergency($message);
//
//        return $response->withJson(
//            $_response,
//            $_response['code']
//        );
//
//    }
}
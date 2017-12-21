<?php

namespace App\Components\Base\Models\Handlers;

use App\Components\Base\Models\BaseExceptionHandler;
//use App\Versions\V1\Api;
//use App\System\HTTP;

class RouteExceptionHandler extends BaseExceptionHandler {

    public function __construct()
    {
        parent::__construct();
    }

//    public function __invoke($request, $response, $exception = null) {
//
//        $api = new Api();
//        $_response = $api->getDefaultResponseAsArray();
//
//        $_response['code'] = HTTP::CODE_NOT_FOUND;
//        $_response['result'] = 'Route ' . $_SERVER['REQUEST_URI']. ' not found';
//        $_response['success'] = false;
//
//        $this->logger->debug($_response['result']);
//
//        return $response->withJson(
//            $_response,
//            $_response['code']
//        );
//
//    }
}
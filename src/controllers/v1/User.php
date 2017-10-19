<?php

namespace App\Controllers\V1;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Modules\Api;

class User
{

    protected $apiVer = 'v1';
    
    public function create(Request $request, Response $response, $args) {

        $pass   = $request->getParam('password');
        $ip     = $request->getAttribute('ip_address');

        $api = new Api($this->apiVer);
        $result =  $api->getUser()->create($ip, $pass);

        return $response->withJson(
            $result,
            $result['code']
        );
    }

    public function get(Request $request, Response $response, $args) {

        $userId = $request->getAttribute('userId');

        $api = new Api($this->apiVer);
        $result = $api->getUser()->get($userId);

        return $response->withJson(
            $result,
            $result['code']
        );
    }
}
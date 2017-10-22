<?php

namespace App\Versions\V1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Versions\V1\Api;

class User extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

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
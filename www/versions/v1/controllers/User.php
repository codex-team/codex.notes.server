<?php

namespace App\Versions\V1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Versions\V1\Api;

use App\Versions\V1\Models\Exceptions\ControllerException;


/**
 * Class User
 * Основные методы по работе с объектом User
 *
 * @see \App\Versions\V1\Models\User;
 * @package App\Versions\V1\Controllers
 */
class User extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Создаем пользователя
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return json
     */
    public function create(Request $request, Response $response, $args) {

        $pass   = $request->getParam('password');
        $ip     = $request->getAttribute('ip_address');

        $api = new Api();

        $result = $api->getUser()->create($ip, $pass);

        return $response->withJson(
            $result,
            $result['code']
        );
    }

    /**
     * Получаем пользователя
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return json
     */
    public function get(Request $request, Response $response, $args) {

        $userId = $request->getAttribute('userId');

        $api = new Api();
        $result = $api->getUser()->get($userId);

        return $response->withJson(
            $result,
            $result['code']
        );
    }
}
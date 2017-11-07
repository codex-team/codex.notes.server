<?php

namespace App\Versions\V1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Versions\V1\Api;

/**
 * Class Folder
 * Основные методы по работе с объектом Folder
 *
 * @see \App\Versions\V1\Models\Folder;
 * @package App\Versions\V1\Controllers
 */
class Folder extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Создаем папку
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return json
     */
    public function create(Request $request, Response $response, $args) {

        $name = $request->getParam('name');
        $user = $request->getParam('user');

        $api = new Api();

        $result = $api->getFolder()->create($user, $name)->getResponse();

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
    public function delete(Request $request, Response $response, $args) {

        $name = $request->getParam('name');
        $user = $request->getParam('user');

        $api = new Api();
        $result = $api->getFolder()->delete($user, $name)->getResponse();

        return $response->withJson(
            $result,
            $result['code']
        );
    }
}
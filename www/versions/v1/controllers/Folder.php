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
     *
     * Получаем в POST
     * @param name          Folder name
     * @param id            Folder id
     * @param user          User id
     * @param timestamp     unix time
     *
     * @return json
     */
    public function create(Request $request, Response $response, $args) {

        $name = $request->getParam('name');
        $id   = $request->getParam('id');
        $user = $request->getParam('user');
        $timestamp = $request->getParam('timestamp');

        $api = new Api();

        $result = $api->getFolder()->create($user, $name, $id, $timestamp)->getResponse();

        return $response->withJson(
            $result,
            $result['code']
        );
    }

    /**
     * Получаем пользователя
     *
     * Получаем в POST
     * @param id    Folder id
     * @param user  User id
     *
     * @return json
     */
    public function delete(Request $request, Response $response, $args) {

        $id = $request->getParam('id');
        $user = $request->getParam('user');

        $api = new Api();
        $result = $api->getFolder()->delete($user, $id)->getResponse();

        return $response->withJson(
            $result,
            $result['code']
        );
    }
}
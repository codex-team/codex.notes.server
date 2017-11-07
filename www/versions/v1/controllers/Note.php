<?php

namespace App\Versions\V1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Versions\V1\Api;

/**
 * Class Note
 * Основные методы по работе с объектом Folder
 *
 * @see \App\Versions\V1\Models\Note;
 * @package App\Versions\V1\Controllers
 */
class Note extends Base
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

        $dirId = $request->getParam('dirId');
        $dirName = $request->getParam('dieName');
        $timestamp = $request->getParam('timestamp');

        $api = new Api();

        $result = $api->getNote()->create($dirId, $dirName, $timestamp)->getResponse();

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
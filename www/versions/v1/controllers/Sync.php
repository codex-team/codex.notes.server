<?php

namespace App\Versions\V1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Versions\V1\Api;

/**
 * Class Sync
 * Основные методы по работе с объектом Sync
 *
 * @see \App\Versions\V1\Models\Sync;
 * @package App\Versions\V1\Controllers
 */
class Sync extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Получаем пользователя
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return json
     */
    public function sync(Request $request, Response $response, $args) {

        $data = $request->getParsedBody();
        $result = array();

        if (isset($data['updates']) && isset($data['dt_sync'])) {
            $result['dt_sync'] = $data['dt_sync'];
            foreach ($data['updates']['folders'] as $folder) {
                var_dump($folder);
            }
        }

        return $response->withJson(
            "",
            200
        );
    }
}
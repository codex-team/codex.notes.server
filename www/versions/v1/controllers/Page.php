<?php

namespace App\Versions\V1\Controllers;

use App\System\Utilities\Message;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class Page
 * Обьект дл работы со статичными страницами сайта
 *
 * @package App\Versions\V1\Controllers
 */
class Page extends Base {

    /**
     * Грузим сообщения, которые далее будем использовать в ответах
     */
    public function __construct()
    {
        $this->messages = Message::load('v1', 'app');
    }

    /**
     * Выводим приколюшку
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return html
     */
    public function index(Request $request, Response $response, $args) {

        return $response->getBody()->write($this->messages['welcome']);
    }
}
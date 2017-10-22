<?php

namespace App\Versions\V1\Controllers;

use App\System\Utilities\Message;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Page extends Base {

    public function __construct()
    {
        $this->messages = Message::load('v1', 'app');
    }

    public function index(Request $request, Response $response, $args) {

        return $response->getBody()->write($this->messages['welcome']);
    }
}
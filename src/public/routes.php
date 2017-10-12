<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

global $app;

/**
 * Создание пользователя
 * @param   GET  $password
 * @return  JSON
 */
$app->post('/user/create', function(Request $request, Response $response) {

    global $messages;

    $pass = $request->getParam('password');
    $ip   = $request->getAttribute('ip_address');

    if (!$pass) {

        return $response->withJson([
            'success' => FALSE,
               'result' => $messages['auth']['password']['empty']
        ], 400);
    }
    else {

        $user = new Models\User();

        return $response->withJson([
            'success' => TRUE,
               'result' => $user->create($ip, $pass)
        ], 200);

    }
});

$app->post('/user/get/{userId}', function(Request $request, Response $response) {

    global $messages;

    $userId = $request->getAttribute('userId');

    if (!$userId) {
        return $response->withJson([
            'success' => FALSE,
               'result' => $messages['auth']['userId']['empty']
        ], 400);
    }
    else {
        $user = new Models\User();

        return $response->withJson([
            'success' => TRUE,
               'result' => $user->get($userId)
        ], 200);
    }
});

$app->get('/', function(Request $request, Response $response) {

    $response->getBody()->write(json_encode('Index page'));
    
    return $response;
});
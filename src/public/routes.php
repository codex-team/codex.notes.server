<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Modules\Api;

global $app;

/**
 * Создание пользователя
 * @return  JSON
 */
$app->get('/{apiVer}/user/create', function(Request $request, Response $response) {

    $user = new Api\User($request, $response);
    $user->create();
    
    return $user->sendResponse();
});

/**
 * Return user info by userId
 * @return JSON  
 */
$app->post('/{apiVer}/user/get/{userId}', function(Request $request, Response $response) {

    $user = new Api\User($request, $response);
    $user->get();

    return $user->sendResponse();
});

/**
 * Site's index page
 */
$app->get('/', function(Request $request, Response $response) {

    global $messages;

    $response->getBody()->write($messages['welcome']);
    
    return $response;
});
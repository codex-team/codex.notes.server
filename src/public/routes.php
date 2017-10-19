<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Modules\Api;

global $app;

/**
 * Создание пользователя
 * @return  JSON
 */
$app->post('/{apiVer}/user/create', function(Request $request, Response $response) {

    $apiVer = $request->getAttribute('apiVer');
    $pass   = $request->getParam('password');
    $ip     = $request->getAttribute('ip_address');

    $api = new Api($apiVer);
    $result =  $api->getUser()->create($ip, $pass);

    return $response->withJson(
        $result,
        $result['code']
    );
});

/**
 * Return user info by userId
 * @return JSON  
 */
$app->post('/{apiVer}/user/get/{userId}', function(Request $request, Response $response) {

    $apiVer = $request->getAttribute('apiVer');
    $userId = $request->getAttribute('userId');

    $api = new Api($apiVer);
    $result = $api->getUser()->get($userId);

    return $response->withJson(
        $result,
        $result['code']
    );
});

/**
 * Site's index page
 */
$app->get('/', function(Request $request, Response $response) {

    global $messages;

    $response->getBody()->write($messages['welcome']);
    
    return $response;
});
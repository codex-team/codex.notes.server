<?php

global $app;

$app->group('/v1', function() {
    $this->post('/user/get/{userId}', 'App\Controllers\V1\User:get');
    $this->post('/user/create',       'App\Controllers\V1\User:create');
});

$app->get('/', function(Request $request, Response $response) {

    global $messages;

    return $response->getBody()->write($messages['welcome']);
});
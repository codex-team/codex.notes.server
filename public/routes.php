<?php

$app->group('/v1', function() {
    $this->get('/',                   'App\Versions\V1\Controllers\Page:index');
    $this->post('/user/get/{userId}', 'App\Versions\V1\Controllers\User:get');
    $this->post('/user/create',       'App\Versions\V1\Controllers\User:create');
});
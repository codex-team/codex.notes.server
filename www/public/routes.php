<?php

/**
 * API v1
 */
$app->group('/v1', function() {
    /**
     * Заглушка
     */
    $this->get('/', 'App\Versions\V1\Controllers\Page:index');

    /**
     * Получаем информацию по id пользователя
     * @param  string {userId}  hex
     * @return json
     */
    $this->post('/user/get/{userId}', 'App\Versions\V1\Controllers\User:get');

    /**
     * Создаем пользователя в MongoDB
     * @param  string {password} Передается в POST
     * @return json
     */
    $this->post('/user/create', 'App\Versions\V1\Controllers\User:create');
});
<?php

/**
 * Internal components
 */
$app->get('/', 'App\Components\Index\Index:page');

/**
 * GraphQL API endpoint
 */
$app->map(['GET', 'POST'], '/graphql', 'App\Components\Api\Api:graphql');

/**
 * API v1
 * @deprecated
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

    $this->post('/folder/create', 'App\Versions\V1\Controllers\Folder:create');
    $this->post('/folder/delete', 'App\Versions\V1\Controllers\Folder:delete');

    $this->post('/folder/addCollaborator', 'App\Versions\V1\Controllers\Folder:addCollaborator');
    $this->post('/folder/verifyCollaborator', 'App\Versions\V1\Controllers\Folder:verifyCollaborator');

});

<?php

use App\System\Config;

/**
 * Internal components
 */
$app->get('/', 'App\Components\Index\Index:page');
$app->get('/join/{ownerId}/{folderId}/{token}', 'App\Components\Index\Index:join');

/**
 * Generate JWT auth token
 */
$app->get('/oauth/code', 'App\Components\OAuth\OAuth:code');
$app->get('/oauth/mobile', 'App\Components\OAuth\OAuth:mobile');

/**
 * GraphQL API endpoint
 */
$graphqlRoute = $app->map(['GET', 'POST'], '/graphql', 'App\Components\Api\Api:graphql');

/**
 * Always load JWT Auth middleware except if 'JWT_AUTH=FALSE' is set in the .env file
 */
if (Config::getBool('JWT_AUTH')) {
    $graphqlRoute->add('App\Components\Middleware\Auth:jwt');
}

<?php

/**
 * Internal components
 */
$app->get('/', 'App\Components\Index\Index:page');
$app->get('/oauth/code', 'App\Components\OAuth\OAuth:code');

/**
 * GraphQL API endpoint
 */
$app->map(['GET', 'POST'], '/graphql', 'App\Components\Api\Api:graphql')
    ->add('App\Components\Middleware\Auth:jwt');
<?php

/**
 * Internal components
 */
$app->get('/', 'App\Components\Index\Index:page');
$app->get('/oauth/code', 'App\Components\OAuth\OAuth:code');

/**
 * GraphQL API endpoint
 */
$graphqlRoute = $app->map(['GET', 'POST'], '/graphql', 'App\Components\Api\Api:graphql');

/**
 * Always load JWT Auth middleware except if 'JWT_AUTH=FALSE' is set in the .env file
 */
//if (($_ENV['JWT_AUTH'] ?? "TRUE") !== "FALSE") {
//    $graphqlRoute->add('App\Components\Middleware\Auth:jwt');
//}

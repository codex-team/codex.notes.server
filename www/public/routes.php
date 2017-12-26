<?php

/**
 * Internal components
 */
$app->get('/', 'App\Components\Index\Index:page');

/**
 * GraphQL API endpoint
 */
//$app->map(['GET', 'POST'], '/graphql', 'App\Components\Api\Api:graphql');
$app->post('/graphql', 'App\Components\Api\Api:graphql');
//$app->get('/graphql', 'App\Components\Api\Api:graphql');
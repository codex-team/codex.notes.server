<?php

namespace App\Versions\V1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Versions\V1\Api;
use App\Versions\V1\Schema\QueryType;


use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;


use GraphQL\GraphQL;
use GraphQL\Type\Schema;

/**
 s
 */
class Users extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Получаем пользователя
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return json
     */
    public function test(Request $request, Response $response, $args) {

        // $userId = $request->getAttribute('userId');

        // $api = new Api();
        // $result = $api->getUser()->get($userId)->getResponse();
        //
        $requestBody = json_decode($request->getBody(), true);

        $schema = new Schema([
            'query' => new QueryType()
        ]);

        $query = $requestBody['query'];
        $variableValues = $requestBody['variables'] ?? null;


        try {
            $rootValue = ['prefix' => 'You said: '];
            $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $output = $result->toArray();
        } catch (\Exception $e) {
            $output = [
                'errors' => [
                    [
                        'message' => $e->getMessage()
                    ]
                ]
            ];
        }
        var_dump($output);


        // return $response->withJson(
        //     ['r' => $output],
        //     200
        // );
    }
}
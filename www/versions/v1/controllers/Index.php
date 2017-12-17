<?php

namespace App\Versions\V1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Versions\V1\Schema\Query;


use GraphQL\GraphQL;
use GraphQL\Type\Schema;

/**
s
 */
class Index extends Base
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
     * @return JSON
     */
    public function graphql(Request $request, Response $response, $args) {

        $requestBody = $request->getBody();

        /**
         * Save request to the logs
         */
        $this->logger->debug($requestBody);

        /**
         * Parse request body
         */
        $requestBody = json_decode($requestBody, true);

        /**
         * Extract the Query
         */
        $query = $requestBody['query'];

        /**
         * Extract variables
         */
        $variables = $requestBody['variables'] ?? null;

        /**
         * Optional: operationName is only required if multiple operations are present in the query.
         */
        $operationName = $requestBody['operationName'] ?? null;

        $this->logger->debug('Content-Type: ' . json_encode($request->getHeader('Content-Type')));
        $this->logger->debug( 'Query: ' . $query);


//        $schema = new Schema([
//            'query' => new Query()
//        ]);

        /**
         * Returning JSON in GraphQL preferred format
         * @see http://graphql.org/learn/serving-over-http/#response
         */
        $output = [
            'data' => [],
            'errors' => []
        ];

        $this->server->processPsrRequest($request, $response, $response->getBody());

//        try {
//
//            $rootValue = ['prefix' => 'You said: '];
//            $result = GraphQL::executeQuery($this->schema, $query, $rootValue, null, $variables);
//            $output = $result->toArray();
//
//        } catch (\Exception $e) {
//            $output['errors'] = [
//                [
//                    'message' => $e->getMessage()
//                ]
//            ];
//        }
//        var_dump($output);

        /**
         * If there were no errors returned, the "errors" field should not be present on the response
         * @see http://graphql.org/learn/serving-over-http/#response
         */
//        if (empty($output['errors'])){
//            unset($output['errors']);
//        }

//         return $response->withJson(
//             [
//                 'data' => $output,
//                 'errors' => []
//             ],
//             200
//         );
    }
}
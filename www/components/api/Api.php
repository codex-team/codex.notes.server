<?php

namespace App\Components\Api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\{
    Schema\Types,
    System\Log,
    System\Utilities\Config
};
use GraphQL\{
    Type\Schema,
    Server\StandardServer,
    Server\ServerConfig,
    Error\Debug
};


/**
 * Class Api
 * @package App\Components\Api
 */
class Api
{
    protected $logger;

    /**
     * Current GraphQL schema
     */
    protected $schema;

    /**
     * graphql-php Standard Server instance.
     * It supports more features out of the box, including parsing HTTP requests,
     * producing a spec-compliant response; batched queries; persisted queries.
     * @see http://webonyx.github.io/graphql-php/executing-queries/#using-server
     */
    protected $server;

    public function __construct()
    {
        if (!$this->logger) {
            $this->logger = new Log();
        }

        $this->schema = new Schema([
            'query' => Types::query(),
        ]);
    }

    /**
     * Single endpoint for all GraphQL queries to the API
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function graphql(Request $request, Response $response, $args) {

        $requestBody = $request->getBody();

        /**
         * Save request to the logs
         */
        $this->logger->debug($requestBody);

        /**
         * Configure server
         * @see https://github.com/webonyx/graphql-php/blob/master/docs/reference.md#graphqlserverserverconfig
         */
        $config = ServerConfig::create()
            ->setSchema($this->schema);

        /**
         * Enable debugging tools
         * @see https://github.com/webonyx/graphql-php/blob/master/docs/error-handling.md#default-error-formatting
         */
        if (Config::debug()){
            /**
             * Show original error message instead of 'Internal server error'
             */
            $config->setDebug(Debug::INCLUDE_DEBUG_MESSAGE);
        } else {
            /**
             * Continue error throwing to the logs/log_YYYY-MM-DD.txt
             */
             $config->setDebug(Debug::RETHROW_INTERNAL_EXCEPTIONS);
        }

        /**
         * Pass request to the GraphQL Standard Server
         */
        $this->server = new StandardServer($config);

        return $this->server->processPsrRequest($request, $response, $requestBody);

    }
}
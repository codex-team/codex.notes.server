<?php

namespace App\Components\Api;

use App\Schema\Types;
use App\System\{
    Config,
    Log
};
use GraphQL\Error\{
    Debug,
    FormattedError
};
use GraphQL\Executor\ExecutionResult;
use GraphQL\Server\{
    ServerConfig,
    StandardServer
};
use GraphQL\Type\Schema;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryDepth;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class Api
 *
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
     *
     * @see http://webonyx.github.io/graphql-php/executing-queries/#using-server
     */
    protected $server;

    public function __construct()
    {
        if (!$this->logger) {
            $this->logger = Log::instance();
        }

        $this->schema = new Schema([
            'query' => Types::query(),
            'mutation' => Types::mutation()
        ]);

        /**
         * Configure server
         *
         * @see https://github.com/webonyx/graphql-php/blob/master/docs/reference.md#graphqlserverserverconfig
         */
        $config = ServerConfig::create()
            ->setSchema($this->schema)
            ->setErrorFormatter(function ($e) {
                $message = sprintf("%s in %s:%s\n%s", $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
                $this->logger->error($message);

                return FormattedError::createFromException($e);
            });

        /**
         * Enable debugging tools
         *
         * @see https://github.com/webonyx/graphql-php/blob/master/docs/error-handling.md#default-error-formatting
         */
        if (Config::debug()) {
            /**
             * Continue error throwing to the logs/log_YYYY-MM-DD.txt
             */
            $config->setDebug(Debug::INCLUDE_DEBUG_MESSAGE);
        } else {
            /**
             * Show original error message instead of 'Internal server error'
             */
            $config->setDebug(Debug::RETHROW_INTERNAL_EXCEPTIONS);
        }

        /**
         * Pass request to the GraphQL Standard Server
         */
        $this->server = new StandardServer($config);
    }

    /**
     * Single endpoint for all GraphQL queries to the API
     *
     * @param Request  $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     */
    public function graphql(Request $request, Response $response, $args)
    {
        /**
         * Save request to the logs
         */
        $this->logger->debug($request->getBody());

        /**
         * Set request max depth level
         */
        $rule = new QueryDepth($maxDepth = Config::get('MAX_QUERY_DEPTH'));
        DocumentValidator::addRule($rule);

        /** @var ExecutionResult|ExecutionResult[] $result */
        $result = $this->server->executePsrRequest($request);

        $body = $response->getBody();
        $body->write(json_encode($result));

        $newResponse = $response->withHeader('Content-type', 'application/json');

        return $newResponse;
    }

//    /**
//     * Single endpoint for all GraphQL queries to the API
//     * @param Request $request
//     * @param Response $response
//     * @param $args
//     */
//    public function graphql(Request $request, Response $response, $args)
//    {
//        $this->server->handleRequest();
//    }
}

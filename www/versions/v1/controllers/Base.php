<?php

namespace App\Versions\V1\Controllers;

use App\System\Log;
use App\System\Utilities\Config;
use GraphQL\Utils\BuildSchema;
use GraphQL\Server\StandardServer;


/**
 * Class Base
 * Родитель для остальных контроллеров
 *
 * @package App\Versions\V1\Controllers
 */
class Base {

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

    public function __construct() {
        if (!$this->logger) {
            $this->logger = new Log();
        }

        $schemaFile = file_get_contents( Config::getPathTo('versions') . '/v1/schema/schema.graphqls');

        if ($schemaFile) {

            try {
                $typeConfigDecorator = function($typeConfig, $typeDefinitionNode) {
                    $name = $typeConfig['name'];
                    switch ($name) {
                        case 'Query':
                            $typeConfig['resolveField'] = function($value, $args, $context, $info) {
                                dump($value);
                                dump($args);
                                return $value->{$info->fieldName};
                            };
                            break;
                    }
                    // ... add missing options to $typeConfig based on type $name
                    return $typeConfig;
                };
                $this->schema = BuildSchema::build($schemaFile, $typeConfigDecorator);
            } catch (\Exception $e){
                $this->logger->error($e);
            }
        } else {
            $this->logger->error('Schema file was not found');
        }

        $this->server = new StandardServer([
            'schema' => $this->schema
        ]);

    }
}
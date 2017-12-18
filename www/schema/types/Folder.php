<?php

namespace App\Schema\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Schema\Types;


/**
 * Folder type
 * @package App\Schema\Types
 */
class Folder extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function() {
                return [
                    'id' => [
                        'type' => Type::id(),
                        'description' => 'Folder\'s unique identifier',
                    ],
                    'title' => [
                        'type' => Type::string(),
                        'description' => 'Folder\'s public title',
                    ],
                    'dt_create' => [
                        'type' => Type::int(),
                        'description' => 'Folder\'s creation timestamp',
                    ],
                    'dt_modify' => [
                        'type' => Type::int(),
                        'description' => 'Folder\'s last modification timestamp',
                    ],
                    'is_shared' => [
                        'type' => Type::boolean(),
                        'description' => 'Shared status: false on creation, true on sharing',
                    ],
                    'owner' => [
                        'type' => Types::user(),
                        'description' => 'Person who create a folder',
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}
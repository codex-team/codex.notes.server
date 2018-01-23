<?php

namespace App\Schema\Types;

use GraphQL\Type\Definition\{
    ObjectType,
    Type
};
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
                    'owner' => [
                        'type' => Types::user(),
                        'description' => 'Person who create a folder',
                    ],
                    'dtCreate' => [
                        'type' => Type::int(),
                        'description' => 'Folder\'s creation timestamp',
                    ],
                    'dtModify' => [
                        'type' => Type::int(),
                        'description' => 'Folder\'s last modification timestamp',
                    ],
                    'isShared' => [
                        'type' => Type::boolean(),
                        'description' => 'Shared status: false on creation, true on sharing',
                    ],
                    'isRemoved' => [
                        'type' => Type::boolean(),
                        'description' => 'Removed status: true if Folder marked as removed',
                    ],
                    'isRoot' => [
                        'type' => Type::boolean(),
                        'description' => 'true if this Folder is Root',
                    ],
                    'notes' => [
                        'type' => Type::listOf(Types::note()),
                        'description' => 'Notes list',

                        /** @todo make it work */
                        'args' => [
                            'limit' => [
                                'type' => Type::int(),
                                'defaultValue' => 0
                            ],
                            'skip' => [
                                'type' => Type::int(),
                                'defaultValue' => 0
                            ]
                        ],
                    ],
                    'collaborators' => [
                        'type' => Type::listOf(Types::collaborator()),
                        'description' => 'List of collaborators'
                    ]
                ];
            }
        ];

        parent::__construct($config);
    }
}
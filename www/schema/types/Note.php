<?php

namespace App\Schema\Types;

use GraphQL\Type\Definition\{
    ObjectType,
    Type
};
use App\Schema\Types;

/**
 * Note type
 * @package App\Schema\Types
 */
class Note extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function() {
                return [
                    'id' => [
                        'type' => Type::id(),
                        'description' => 'Note\'s unique identifier',
                    ],
                    'title' => [
                        'type' => Type::string(),
                        'description' => 'Note\'s public title',
                    ],
                    'dt_create' => [
                        'type' => Type::int(),
                        'description' => 'Note\'s creation timestamp',
                    ],
                    'dt_modify' => [
                        'type' => Type::int(),
                        'description' => 'Note\'s last modification timestamp',
                    ],
                    'content' => [
                        'type' => Type::string(),
                        'description' => 'Note\'s content in the JSON-format',
                    ],
                    'author' => [
                        'type' => Types::user(),
                        'description' => 'Note\'s author',
                    ],
                    'views' => [
                        'type' => Type::int(),
                        'description' => 'Note\'s views counter',
                    ]
                ];
            }
        ];

        parent::__construct($config);
    }
}
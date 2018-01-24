<?php

namespace App\Schema\Types;

use App\Schema\Types;
use GraphQL\Type\Definition\{
    ObjectType,
    Type
};

/**
 * Note type
 *
 * @package App\Schema\Types
 */
class Note extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::id(),
                        'description' => 'Note\'s unique identifier',
                    ],
                    'title' => [
                        'type' => Type::string(),
                        'description' => 'Note\'s public title',
                    ],
                    'content' => [
                        'type' => Type::string(),
                        'description' => 'Note\'s content in the JSON-format',
                    ],
                    'dtCreate' => [
                        'type' => Type::int(),
                        'description' => 'Note\'s creation timestamp',
                    ],
                    'dtModify' => [
                        'type' => Type::int(),
                        'description' => 'Note\'s last modification timestamp',
                    ],
                    'author' => [
                        'type' => Types::user(),
                        'description' => 'Note\'s author',
                    ],
                    'isRemoved' => [
                        'type' => Type::boolean(),
                        'description' => 'Removed status: true if Note marked as removed',
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}

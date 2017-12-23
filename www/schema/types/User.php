<?php

namespace App\Schema\Types;

use GraphQL\Type\Definition\{
    ObjectType,
    Type
};
use App\Schema\Types;

/**
 * User type
 * @package App\Schema\Types
 */
class User extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function() {
                return [
                    'id' => [
                        'type' => Type::id(),
                        'description' => 'User\'s unique identifier',
                    ],
                    'name' => [
                        'type' => Type::string(),
                        'description' => 'User\'s nickname',
                    ],
                    'email' => [
                        'type' => Type::string(),
                        'description' => 'User\'s email address',
                    ],
                    'dt_reg' => [
                        'type' => Type::int(),
                        'description' => 'User\'s register timestamp',
                    ],
                    'folders' => [
                        'type' => Type::listOf(Types::folder()),
                        'description' => 'User\'s email address',
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}
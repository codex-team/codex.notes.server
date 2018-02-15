<?php

namespace App\Schema\Types;

use App\Schema\Types;
use GraphQL\Type\Definition\{
    ObjectType,
    Type
};

/**
 * User type
 *
 * @package App\Schema\Types
 */
class User extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function () {
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
                    'photo' => [
                        'type' => Type::string(),
                        'description' => 'User\'s avatar',
                    ],
                    'googleId' => [
                        'type' => Type::string(),
                        'description' => 'User\'s google id',
                    ],
                    'dtSync' => [
                        'type' => Type::int(),
                        'description' => 'User\'s synchronization',
                    ]
                    'dtReg' => [
                        'type' => Type::int(),
                        'description' => 'User\'s register timestamp',
                    ],
                    'dtModify' => [
                        'type' => Type::int(),
                        'description' => 'User\'s last modification timestamp',
                    ],
                    'folders' => [
                        'type' => Type::listOf(Types::folder()),
                        'description' => 'User\'s folders',

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
                ];
            }
        ];

        parent::__construct($config);
    }
}

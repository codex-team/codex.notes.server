<?php

namespace App\Schema\Types;

use App\Components\Api\Models as Models;
use App\Components\Middleware\Auth;
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
            'name' => 'UserType',
            'description' => 'User\'s data',
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::id(),
                        'description' => 'Unique identifier',
                    ],
                    'name' => [
                        'type' => Type::string(),
                        'description' => 'Full name',
                    ],
                    'email' => [
                        'type' => Type::string(),
                        'description' => 'Email address',
                    ],
                    'photo' => [
                        'type' => Type::string(),
                        'description' => 'Photo URL',
                    ],
                    'googleId' => [
                        'type' => Type::string(),
                        'description' => 'Google ID',
                    ],
                    'dtReg' => [
                        'type' => Type::int(),
                        'description' => 'Timestamp of registration',
                    ],
                    'dtModify' => [
                        'type' => Type::int(),
                        'description' => 'Last modification timestamp',
                    ],
                    'folders' => [
                        'type' => Type::listOf(Types::folder()),
                        'description' => 'Folders list',
                        'args' => [
                            'limit' => [
                                'type' => Type::int(),
                                'description' => 'Folders limit',
                                'defaultValue' => null
                            ],
                            'skip' => [
                                'type' => Type::int(),
                                'description' => 'Skip that number of Folders',
                                'defaultValue' => null
                            ]
                        ],
                        'resolve' => function ($user, $args) {
                            if (!Auth::checkUserAccess($user->id)) {
                                throw new \Exception('Access denied');
                            }


                            /**
                             * Create an empty User model
                             */
                            $userModel = new Models\User();

                            /** Set User's id */
                            $userModel->id = $user->id;

                            $limit = $args['limit'];
                            $skip = $args['skip'];

                            if ($userModel->id) {
                                $userModel->fillFolders($limit, $skip);

                            }

                            return $userModel->folders;
                        }
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}

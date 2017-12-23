<?php

namespace App\Schema\Types;

use GraphQL\Type\Definition\{
    ObjectType,
    Type
};
use App\Schema\Types;
use App\Components\Api\Models\{
    User,
//    Notes,
    Folder
};

/**
 * Class Mutation
 * @package App\Schema\Types
 *
 * Mutation type for GraphQL schema
 */
class Mutation extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function() {
                return [

                    'user' => [
                        'type' => Types::user(),
                        'description' => 'Sync folder',
                        'args' => [
                            'id'         => Type::nonNull(Type::id()),
                            'name'       => Type::nonNull(Type::string()),
                            'email'      => Type::nonNull(Type::string()),
                            'dt_reg'     => Type::int()
                        ],
                        'resolve' => function($root, $args) {

                            $user = new User();

                            $user->sync($args);

                            return $user;
                        }
                    ],

                    'folder' => [
                        'type' => Types::folder(),
                        'description' => 'Sync folder',
                        'args' => [
                            'id'         => Type::nonNull(Type::id()),
                            'owner'      => Type::nonNull(Type::id()),
                            'title'      => Type::nonNull(Type::string()),
                            'dt_create'  => Type::int(),
                            'dt_modify'  => Type::int(),
                            'is_shared'  => Type::boolean(),
                            'is_removed' => Type::boolean()
                        ],
                        'resolve' => function($root, $args) {

                            $folder = new Folder($args['owner']);

                            $folder->sync($args);

                            return $folder;
                        }
                    ],

                ];
            }
        ];

        parent::__construct($config);
    }
}

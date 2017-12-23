<?php

namespace App\Schema\Types;

use GraphQL\Type\Definition\{
    ObjectType,
    ResolveInfo,
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
                            'id'    => Type::nonNull(Type::id()),
                            'name'  => Type::nonNull(Type::string()),
                            'email' => Type::nonNull(Type::string()),
                            'dtReg' => Type::int()
                        ],
                        'resolve' => function($root, $args, $context, ResolveInfo $info) {

                            $selectedFields = $info->getFieldSelection();

                            $user = new User();
                            $user->sync($args);

                            if (in_array('folder', $selectedFields)) {

                                $user->getFolders();
                            }

                            return $user;
                        }
                    ],

                    'folder' => [
                        'type' => Types::folder(),
                        'description' => 'Sync folder',
                        'args' => [
                            'id'        => Type::nonNull(Type::id()),
                            'ownerId'   => Type::nonNull(Type::id()),
                            'title'     => Type::nonNull(Type::string()),
                            'dtCreate'  => Type::int(),
                            'dtModify'  => Type::int(),
                            'isShared'  => Type::boolean(),
                            'isRemoved' => Type::boolean()
                        ],
                        'resolve' => function($root, $args) {

                            $folder = new Folder($args['ownerId']);

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

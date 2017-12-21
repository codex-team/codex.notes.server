<?php

namespace App\Schema\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Schema\Types;
use App\Components\Api\Models\{
    User,
    Notes,
    Folders
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
                    'createFolder' => [
                        'type' => Types::folder(),
                        'description' => 'Create a new folder',
                        'args' => [
                            'owner' => Type::nonNull(Type::id()),
                            'title' => Type::nonNull(Type::string()),
                            'dt_create' => Type::nonNull(Type::int()),
                            'dt_modify' => Type::int(),
                            'is_shared' => Type::boolean()
                        ],
                        'resolve' => function($root, $args){
                            $userFolders = new Folders($args['owner']);

                            $folder = $userFolders->create($args);

                            return $folder;
                        }
                    ],

                    'renameFolder' => [
                        'type' => Type::boolean(),
                        'description' => 'Rename folder by id',
                        'args' => [
                            'owner' => Type::nonNull(Type::id()),
                            'id' => Type::nonNull(Type::id()),
                            'title' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => function($root, $args) {
                            $userFolders = new Folders($args['owner']);

                            $result = $userFolders->rename($args);

                            return ['ok' => $result];
                        }
                    ],

                    'deleteFolder' => [
                         'type' => Type::boolean(),
                         'description' => 'Delete folder by id',
                         'args' => [
                             'owner' => Type::nonNull(Type::id()),
                             'id' => Type::nonNull(Type::id())
                         ],
                         'resolve' => function($root, $args) {
                             $userFolders = new Folders($args['owner']);

                             $result = $userFolders->delete($args);

                             return ['ok' => $result];
                         }
                    ]


                    // @todo updateFolderTitle

                ];
            }
        ];

        parent::__construct($config);
    }
}

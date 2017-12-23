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
 * Class Query
 * @package App\Schema\Types
 *
 * Query type for GraphQL schema
 */
class Query extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function() {
                return [
                    'user' => [
                        'type' => Types::user(),
                        'description' => 'Return user by id',
                        'args' => [
                            'id' => Type::nonNull(Type::id()),
                            'foldersLimit' => [
                                'type' => Type::int(),
                                'defaultValue' => 0
                            ],
                            'foldersSkip' => [
                                'type' => Type::int(),
                                'defaultValue' => 0
                            ]
                        ],
                        'resolve' => function($root, $args) {

                            $user = new User($args['id']);

                            $limit = $args['foldersLimit'];
                            $skip = $args['foldersSkip'];

                            if ($user->id && $limit !== null) {

                                $user->getFolders($limit, $skip);
                            }

                            return $user;
                        }
                    ],

                    // 'note' => [
                    //     'type' => Types::note(),
                    //     'description' => 'Return note by id',
                    //     'args' => [
                    //         'id' => Type::nonNull(Type::id()),
                    //     ],
                    //     'resolve' => function($root, $args) {
                    //         return new Note($args['id']);
                    //     }
                    // ],

//                    'notes' => [
//                        'type' => Type::listOf(Types::note()),
//                        'description' => 'List of notes by user id',
//                        'args' => [
//                            'userId' => Type::nonNull(Type::id()),
//                            'folderId' => Type::nonNull(Type::id()),
//                        ],
//                        'resolve' => function($root, $args) {
//                            $NotesModel = new Notes($args['userId'], $args['folderId']);
//
//                            return $NotesModel->items;
//                        }
//                    ],

                    'folder' => [
                        'type' => Types::folder(),
                        'description' => 'Folder\'s data',
                        'args' => [
                            'ownerId' => Type::nonNull(Type::id()),
                            'id'      => Type::nonNull(Type::id()),
                        ],
                        'resolve' => function($root, $args) {

                            $folder = new Folder($args['ownerId'], $args['id']);

                            return $folder;
                        }
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}

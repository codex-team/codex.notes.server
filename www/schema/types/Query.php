<?php

namespace App\Schema\Types;

use GraphQL\Type\Definition\{
    ObjectType,
    Type
};
use App\Schema\Types;
use App\Components\Api\Models\{
    User,
    Notes,
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
                            'foldersLimit' => Type::int(),
                            'foldersSkip'  => Type::int()
                        ],
                        'resolve' => function($root, $args) {

                            $user = new User($args['id']);

                            if ($user->id && $args['foldersLimit'] !== null) {

                                $user->getFolders($args['foldersLimit'], $args['foldersSkip']);
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
                            'owner' => Type::nonNull(Type::id()),
                            'id' => Type::nonNull(Type::id()),
                        ],
                        'resolve' => function($root, $args) {

                            $folder = new Folder($args['owner'], $args['id']);

                            return $folder;
                        }
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}

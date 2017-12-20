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
                    // 'user' => [
                    //     'type' => Types::user(),
                    //     'description' => 'Return user by id',
                    //     'args' => [
                    //         'id' => Type::nonNull(Type::int()),
                    //     ],
                    //     'resolve' => function($root, $args) {
                    //         return new User($args['id']);
                    //     }
                    // ],
                    //
                    // 'notes' => [
                    //     'type' => Type::listOf(Types::note()),
                    //     'description' => 'List of notes by user id',
                    //     'args' => [
                    //         'userId' => Type::nonNull(Type::id()),
                    //         'folderId' => Type::nonNull(Type::id()),
                    //     ],
                    //     'resolve' => function($root, $args) {
                    //         $NotesModel = new Notes($args['userId'], $args['folderId']);
                    //
                    //         return $NotesModel->items;
                    //     }
                    // ],

                    'folder' => [
                        'type' => Types::folder(),
                        'description' => 'Create a new folder',
                        'args' => [
                            'id' => Type::int(),
                            'userId' => Type::nonNull(Type::id()),
                            'title' => Type::nonNull(Type::string()),
                            'dt_create' => Type::nonNull(Type::int()),
                            'dt_modify' => Type::int(),
                            'is_shared' => Type::boolean(),
                            'owner' => Type::nonNull(Type::id())
                        ],
                        'resolve' => function($root, $args){
                            $userFolders = new Folders($args['userId']);

                            if (true) {
                                return $userFolders->insert($args);
                            // } else {
                            //     $userFolders->update();
                            }

                            // return $FoldersModel->items;
                            // return
                        }
                    ]

                    // 'folders' => [
                    //     'type' => Type::listOf(Types::folder()),
                    //     'description' => 'List of user\'s folders',
                    //     'args' => [
                    //         'userId' => Type::nonNull(Type::id()),
                    //     ],
                    //     'resolve' => function($root, $args){
                    //         $FoldersModel = new Folders($args['userId']);
                    //
                    //         return $FoldersModel->items;
                    //     }
                    // ]
                ];
            }
        ];

        parent::__construct($config);
    }
}

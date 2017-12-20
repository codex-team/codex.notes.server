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
                            'id' => Type::nonNull(Type::int()),
                        ],
                        'resolve' => function($root, $args) {
                            return new User($args['id']);
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

                    'notes' => [
                        'type' => Type::listOf(Types::note()),
                        'description' => 'List of notes by user id',
                        'args' => [
                            'userId' => Type::nonNull(Type::id()),
                            'folderId' => Type::nonNull(Type::id()),
                        ],
                        'resolve' => function($root, $args) {
                            $NotesModel = new Notes($args['userId'], $args['folderId']);

                            return $NotesModel->items;
                        }
                    ],

                    'folders' => [
                        'type' => Type::listOf(Types::folder()),
                        'description' => 'List of user\'s folders',
                        'args' => [
                            'userId' => Type::nonNull(Type::id()),
                        ],
                        'resolve' => function($root, $args){
                            $FoldersModel = new Folders($args['userId']);

                            return $FoldersModel->items;
                        }
                    ]
                ];
            }
        ];

        parent::__construct($config);
    }
}

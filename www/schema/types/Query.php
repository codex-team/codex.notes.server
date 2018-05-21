<?php

namespace App\Schema\Types;

use App\Components\Api\Models\{
    Collaborator,
    Folder,
    Note,
    User
};
use App\Schema\Types;
use GraphQL\Type\Definition\{
    ObjectType,
    ResolveInfo,
    Type
};

/**
 * Class Query
 *
 * @package App\Schema\Types
 *
 * Query type for GraphQL schema
 */
class Query extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'user' => [
                        'type' => Types::user(),
                        'description' => 'Get User\'s data',
                        'args' => [
                            'id' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => 'User\'s Id'
                            ]
                        ],
                        'resolve' => function ($root, $args) {
                            /** Get filled User model */
                            $userModel = new User($args['id']);

                            return $userModel;
                        }
                    ],

                    'folder' => [
                        'type' => Types::folder(),
                        'description' => 'Get Folder\'s data',
                        'args' => [
                            'id' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => 'Folder\'s Id',
                            ],
                            'ownerId' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => 'Folder Owner\'s Id',
                            ]
                        ],
                        'resolve' => function ($root, $args) {
                            /** Get filled Folder model */
                            $folderModel = new Folder($args['ownerId'], $args['id']);

                            return $folderModel;
                        }
                    ],

                    'note' => [
                        'type' => Types::note(),
                        'description' => 'Get Note\'s data',
                        'args' => [
                            'authorId' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => 'Folder Owner\'s Id',
                            ],
                            'folderId' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => 'Folder\'s Id',
                            ],
                            'id' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => 'Note\'s Id',
                            ]
                        ],
                        'resolve' => function ($root, $args) {
                            /** Get filled Note model */
                            $noteModel = new Note($args['authorId'], $args['folderId'], $args['id']);

                            return $noteModel;
                        }
                    ],

                    'collaborator' => [
                        'type' => Types::collaborator(),
                        'description' => 'Get Collaborator\'s data',
                        'args' => [
                            'ownerId' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => 'Folder Owner\'s Id',
                            ],
                            'folderId' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => 'Folder\'s Id',
                            ],
                            'token' => [
                                'type' => Type::nonNull(Type::string()),
                                'description' => 'Collaborator\'s token',
                            ]
                        ],
                        'resolve' => function ($root, $args) {
                            $folderModel = new Folder($args['ownerId'], $args['folderId']);

                            return new Collaborator($folderModel, $args['token']);
                        }
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}

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
    Note,
    Folder,
    Collaborator
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
                        'description' => 'Return User by id',
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
                                $user->fillFolders($limit, $skip);
                            }

                            return $user;
                        }
                    ],

                    'folder' => [
                        'type' => Types::folder(),
                        'description' => 'Return Folder by id',
                        'args' => [
                            'ownerId' => Type::nonNull(Type::id()),
                            'id' => Type::nonNull(Type::id()),
                            'withOwner' => [
                                'type' => Type::boolean(),
                                'defaultValue' => false
                            ],
                            'withNotes' => [
                                'type' => Type::boolean(),
                                'defaultValue' => false
                            ]
                        ],
                        'resolve' => function($root, $args) {

                            $folder = new Folder($args['ownerId'], $args['id']);

                            if ($args['withOwner']) {
                                $folder->fillOwner();
                            }

                            if ($args['withNotes']) {
                                $folder->fillNotes();
                            }

                            return $folder;
                        }
                    ],

                    'note' => [
                        'type' => Types::note(),
                        'description' => 'Return Note by id',
                        'args' => [
                            'authorId' => Type::nonNull(Type::id()),
                            'folderId' => Type::nonNull(Type::id()),
                            'id' => Type::nonNull(Type::id()),
                            'withAuthor' => [
                                'type' => Type::boolean(),
                                'defaultValue' => false
                            ]
                        ],
                        'resolve' => function($root, $args) {

                            $note = new Note($args['authorId'], $args['folderId'], $args['id']);

                            if ($args['withAuthor']) {
                                $note->fillAuthor();
                            }

                            return $note;
                        }
                    ],

                    'collaborator' => [
                        'type' => Types::collaborator(),
                        'description' => 'Return Collaborator by id',
                        'args' => [
                            'ownerId' => Type::nonNull(Type::id()),
                            'folderId' => Type::nonNull(Type::id()),
                            'id' => Type::nonNull(Type::id()),
                        ],
                        'resolve' => function($root, $args) {

                            $collaborator = new Collaborator($args['ownerId'], $args['folderId'], $args['id']);

                            $collaborator->fillUser();

                            return $collaborator;
                        }
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}

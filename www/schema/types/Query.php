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
                        'resolve' => function ($root, $args) {
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
                            'id' => Type::nonNull(Type::id())
                        ],
                        'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                            $folder = new Folder($args['ownerId'], $args['id']);

                            $selectedFields = $info->getFieldSelection();

                            if (in_array('collaborators', $selectedFields)) {
                                $folder->fillCollaborators();
                            }

                            if (in_array('owner', $selectedFields)) {
                                $folder->fillOwner();
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
                            'id' => Type::nonNull(Type::id())
                        ],
                        'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                            $note = new Note($args['authorId'], $args['folderId'], $args['id']);

                            return $note;
                        }
                    ],
                    'collaborator' => [
                        'type' => Types::collaborator(),
                        'description' => 'Return Collaborator',
                        'args' => [
                            'ownerId' => Type::nonNull(Type::id()),
                            'folderId' => Type::nonNull(Type::id()),
                            'token' => Type::nonNull(Type::string())
                        ],
                        'resolve' => function ($root, $args) {
                            $folder = new Folder($args['ownerId'], $args['folderId']);

                            return new Collaborator($folder, $args['token']);
                        }
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}

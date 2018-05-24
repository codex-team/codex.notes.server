<?php

namespace App\Schema\Types;

use App\Components\Api\Models as Models;
use App\Schema\Types;
use GraphQL\Type\Definition\{
    ObjectType,
    Type
};

/**
 * Folder type
 *
 * @package App\Schema\Types
 */
class Folder extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'FolderType',
            'description' => 'Folder\'s data',
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::id(),
                        'description' => 'Unique identifier',
                    ],
                    'title' => [
                        'type' => Type::string(),
                        'description' => 'Title',
                    ],
                    'owner' => [
                        'type' => Types::user(),
                        'description' => 'User who created a folder',
                        'resolve' => function ($folder) {
                            /** Get filled User model */
                            $userModel = new Models\User($folder->ownerId);

                            return $userModel;
                        }
                    ],
                    'dtCreate' => [
                        'type' => Type::int(),
                        'description' => 'Creation timestamp',
                    ],
                    'dtModify' => [
                        'type' => Type::int(),
                        'description' => 'Timestamp of last modification',
                    ],
                    'isShared' => [
                        'type' => Type::boolean(),
                        'description' => 'Shared status: false on creation, true on sharing',
                    ],
                    'isRemoved' => [
                        'type' => Type::boolean(),
                        'description' => 'Removed status: true if Folder marked as removed',
                    ],
                    'isRoot' => [
                        'type' => Type::boolean(),
                        'description' => 'true if this Folder is Root',
                    ],
                    'notes' => [
                        'type' => Type::listOf(Types::note()),
                        'description' => 'Notes list',
                        'args' => [
                            'limit' => [
                                'type' => Type::int(),
                                'defaultValue' => null
                            ],
                            'skip' => [
                                'type' => Type::int(),
                                'defaultValue' => null
                            ]
                        ],
                        'resolve' => function ($folder, $args) {
                            $folderModel = new Models\Folder($folder->ownerId);
                            $folderModel->id = $folder->id;

                            if (!$folderModel->hasUserAccess($GLOBALS['user']->id)) {
                                throw new \Exception('Access denied');
                            }

                            $limit = $args['limit'];
                            $skip = $args['skip'];

                            $folderModel->fillNotes($limit, $skip);

                            return $folderModel->notes;
                        }
                    ],
                    'collaborators' => [
                        'type' => Type::listOf(Types::collaborator()),
                        'description' => 'List of collaborators',
                        'args' => [
                            'limit' => [
                                'type' => Type::int(),
                                'defaultValue' => null
                            ],
                            'skip' => [
                                'type' => Type::int(),
                                'defaultValue' => null
                            ]
                        ],
                        'resolve' => function ($folder, $args) {
                            $folderModel = new Models\Folder($folder->ownerId);
                            $folderModel->id = $folder->id;

                            if (!$folderModel->hasUserAccess($GLOBALS['user']->id)) {
                                throw new \Exception('Access denied');
                            }

                            $limit = $args['limit'];
                            $skip = $args['skip'];

                            $folderModel->fillCollaborators($limit, $skip);

                            return $folderModel->collaborators;
                        }
                    ]
                ];
            }
        ];

        parent::__construct($config);
    }
}

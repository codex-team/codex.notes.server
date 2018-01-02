<?php

namespace App\Schema\Types;

use App\Components\Base\Models\Exceptions\CollaboratorException;
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
                        'description' => 'Sync User',
                        'args' => [
                            'id' => Type::nonNull(Type::id()),
                            'name' => Type::nonNull(Type::string()),
                            'email' => Type::nonNull(Type::string()),
                            'dtReg' => Type::int()
                        ],
                        'resolve' => function($root, $args, $context, ResolveInfo $info) {

                            $user = new User();
                            $user->sync($args);

                            $selectedFields = $info->getFieldSelection();
                            if (in_array('folders', $selectedFields)) {
                                $user->fillFolders();
                            }

                            return $user;
                        }
                    ],

                    'folder' => [
                        'type' => Types::folder(),
                        'description' => 'Sync Folder',
                        'args' => [
                            'id' => Type::nonNull(Type::id()),
                            'ownerId' => Type::nonNull(Type::id()),
                            'title' => Type::nonNull(Type::string()),
                            'dtCreate' => Type::int(),
                            'dtModify' => Type::int(),
                            'isShared' => Type::boolean(),
                            'isRemoved' => Type::boolean()
                        ],
                        'resolve' => function($root, $args, $context, ResolveInfo $info) {

                            $folder = new Folder($args['ownerId']);
                            $folder->sync($args);

                            $selectedFields = $info->getFieldSelection();

//                            if (in_array('notes', $selectedFields)) {
//                                $folder->fillNotes();
//                            }

                            if (in_array('owner', $selectedFields)) {
                                $folder->fillOwner();
                            }

                            if (in_array('collaborators', $selectedFields)) {
                                $folder->fillCollaborators();
                            }

                            return $folder;
                        }
                    ],

                    'note' => [
                        'type' => Types::note(),
                        'description' => 'Sync Note',
                        'args' => [
                            'id' => Type::nonNull(Type::id()),
                            'authorId' => Type::nonNull(Type::id()),
                            'folderId' => Type::nonNull(Type::id()),
                            'title' => Type::nonNull(Type::string()),
                            'content' => Type::nonNull(Type::string()),
                            'dtCreate' => Type::int(),
                            'dtModify' => Type::int(),
                            'isRemoved' => Type::boolean()
                        ],
                        'resolve' => function($root, $args, $context, ResolveInfo $info) {

                            $note = new Note($args['authorId'], $args['folderId']);
                            $note->sync($args);

                            $selectedFields = $info->getFieldSelection();
                            if (in_array('author', $selectedFields)) {
                                $note->fillAuthor();
                            }

                            return $note;
                        }
                    ],

                    'collaborator' => [
                        'type' => Types::collaborator(),
                        'description' => 'Sync Collaborator',
                        'args' => [
                            'token' => Type::string(),
                            'userId' => Type::id(),
                            'ownerId' => Type::nonNull(Type::id()),
                            'folderId' => Type::nonNull(Type::id()),
                            'email' => Type::nonNull(Type::string()),
                            'dtInvite' => Type::int(),
                            'isRemoved' => Type::boolean()
                        ],
                        'resolve' => function($root, $args, $context, ResolveInfo $info) {

                            try {
                                $folder = new Folder($args['ownerId'], $args['folderId']);

                                $collaborator = new Collaborator($folder, $args['token']);

                                $collaborator->sync($args);

                                $selectedFields = $info->getFieldSelection();
                                if (isset($selectedFields['user'])) {
                                    $collaborator->fillUser();
                                }

                                return $collaborator;
                            } catch (CollaboratorException $e) {
                                return;
                            }
                        }
                    ],

                ];
            }
        ];

        parent::__construct($config);
    }
}

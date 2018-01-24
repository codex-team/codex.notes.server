<?php

namespace App\Schema\Types;

use App\Components\Api\Models\{
    Collaborator,
    Folder,
    Note,
    User
};
use App\Components\Base\Models\Exceptions\{
    AuthException,
    CollaboratorException,
    FolderException
};
use App\Components\Middleware\Auth;
use App\Schema\Types;
use GraphQL\Type\Definition\{
    ObjectType,
    ResolveInfo,
    Type
};

/**
 * Class Mutation
 *
 * @package App\Schema\Types
 *
 * Mutation type for GraphQL schema
 */
class Mutation extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function () {
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
                        'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                            if (!Auth::checkUserAccess($args['id'])) {
                                throw new AuthException('Access denied');
                            }

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
                        'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                            try {
                                if (!Auth::checkUserAccess($args['ownerId'])) {
                                    throw new AuthException('Access denied');
                                }

                                $folder = new Folder($args['ownerId']);
                                $folder->sync($args);

                                $selectedFields = $info->getFieldSelection();

                                if (in_array('owner', $selectedFields)) {
                                    $folder->fillOwner();
                                }

                                if (in_array('collaborators', $selectedFields)) {
                                    $folder->fillCollaborators();
                                }

                                return $folder;
                            } catch (FolderException $e) {
                                return;
                            }
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
                        'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                            if (!Auth::checkUserAccess($args['authorId'])) {
                                throw new AuthException('Access denied');
                            }

                            /**
                             * Get target Folder
                             *
                             * We need to get Folder's Owner. If this Folder
                             * is a Shared, we'll get a real Owner to get right collection
                             */
                            $folder = new Folder($args['authorId'], $args['folderId']);

                            /**
                             * Save Note
                             */
                            $note = new Note($folder->ownerId, $folder->id);
                            $note->sync($args);

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
                            'email' => Type::string(),
                            'dtInvite' => Type::int(),
                            'isRemoved' => Type::boolean()
                        ],
                        'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                            try {
                                if (!Auth::checkUserAccess($args['ownerId'])) {
                                    throw new AuthException('Access denied');
                                }

                                if (!$args['token'] && !$args['email']) {
                                    throw new CollaboratorException('Pass token to verify user or pass email to add a new Collaborator');
                                }

                                /**
                                 * Add a Collaborator to the Shared Folder
                                 */
                                $originalFolder = new Folder($args['ownerId'], $args['folderId']);

                                if (!$originalFolder->ownerId || !$originalFolder->id) {
                                    throw new CollaboratorException('Folder does not exist');
                                }

                                if ($args['token']) {
                                    $collaborator = new Collaborator($originalFolder, $args['token']);
                                    $collaborator->sync($args);

                                    /**
                                     * Accept an Invitation
                                     * Save Shared Folder to the Acceptor's Folders collection
                                     */
                                    if (!empty($args['userId'])) {
                                        $collaborator->saveFolder($originalFolder);
                                    }
                                } elseif ($args['email']) {

                                    /**
                                     * If email field is not null then we need to send an invite
                                     */

                                    $args['token'] = Collaborator::getInvitationToken($args['ownerId'], $args['folderId'], $args['email']);

                                    $collaborator = new Collaborator($originalFolder);
                                    $collaborator->sync($args);

                                    // @todo send an email invite
                                }

                                $selectedFields = $info->getFieldSelection();
                                if (isset($selectedFields['user'])) {
                                    $collaborator->fillUser();
                                }

                                return $collaborator;
                            } catch (CollaboratorException $e) {
                                return;
                            }
                        }
                    ]
                ];
            }
        ];

        parent::__construct($config);
    }
}

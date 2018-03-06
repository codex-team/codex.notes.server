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
    FolderException,
    NoteException
};
use App\Components\Middleware\Auth;
use App\Schema\Types;
use App\System\Log;
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
                            'photo' => Type::string(),
                            'dtReg' => Type::int(),
                            'dtModify' => Type::int()
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
                            'isRemoved' => Type::boolean(),
                            'isRoot' => Type::boolean()
                        ],
                        'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                            try {
                                /** Get real Folder */
                                $folder = new Folder($args['ownerId'], $args['id']);

                                /** Check access to this Folder */
                                $isFolderOwner = Auth::checkUserAccess($folder->ownerId);
                                if (!$isFolderOwner) {

                                    $isCollaborator = $folder->hasUserAccess(Auth::userId());
                                    if (!$isCollaborator) {
                                        throw new AuthException('Access denied');
                                    }
                                }

                                $folder->sync($args);

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
                             * We need to get Folder's Owner.
                             * If this Folder is Shared, we'll get a real Owner to get right collection
                             */
                            $folder = new Folder($args['authorId'], $args['folderId']);

                            if (is_null($folder->id)) {
                                throw new NoteException('Incorrect Folder passed');
                            }

                            /**
                             * Save Note
                             */
                            $note = new Note($folder->ownerId, $folder->id);
                            $note->sync($args);

                            return $note;
                        }
                    ],

                    'invite' => [
                        'type' => Types::collaborator(),
                        'description' => 'Add new collaborator and send invitation email',
                        'args' => [
                            'id' => [
                                'description' => 'Collaborator\'s id',
                                'type' => Type::nonNull(Type::id()),
                            ],
                            'email' => [
                                'description' => 'Collaborator\'s email address',
                                'type' => Type::nonNull(Type::string()),
                            ],
                            'folderId' => [
                                'description' => 'Shared Folder\'s id',
                                'type' => Type::nonNull(Type::id()),
                            ],
                            'ownerId' => [
                                'description' => 'Folder Owner\'s id',
                                'type' => Type::nonNull(Type::id()),
                            ],
                            'dtInvite' => [
                                'description' => 'Date of an invitation sending',
                                'type' => Type::int(),
                            ],
                        ],
                        'resolve' => function ($root, $args) {
                            try {
                                /** Auth check */
                                if (!Auth::checkUserAccess($args['ownerId'])) {
                                    throw new AuthException('Access denied');
                                }

                                /** Get original Folder */
                                $originalFolder = new Folder($args['ownerId'], $args['folderId']);

                                /**
                                 * If Folder has no Collaborators then it has not been shared yet
                                 */
                                if (!$originalFolder->collaborators) {
                                    $ownerUserModel = new User($args['ownerId']);

                                    /** Need to identify Collaborator in DB */
                                    $inviteTokenForOwner = Collaborator::getInvitationToken(
                                        $ownerUserModel->id, $originalFolder->id, $ownerUserModel->email
                                    );

                                    $ownerCollaboratorData = [
                                        'id' => $ownerUserModel->id,        // doesn't matter
                                        'email' => $ownerUserModel->email,
                                        'ownerId' => $ownerUserModel->id,
                                        'folderId' => $originalFolder->id,
                                        'dtInvite' => time(),
                                        'userId' => $ownerUserModel->id,
                                        'token' => $inviteTokenForOwner
                                    ];

                                    /** We should add Owner as a Collaborator */
                                    $ownerCollaboratorModel = new Collaborator($originalFolder);
                                    $ownerCollaboratorModel->sync($ownerCollaboratorData);

                                    /** No need to send invite email to Owner */
                                }

                                $args['token'] = Collaborator::getInvitationToken($args['ownerId'], $args['folderId'], $args['email']);

                                $collaborator = new Collaborator($originalFolder);
                                $collaborator->sync($args);

                                $collaborator->sendInvitationEmail();

                                return $collaborator;
                            } catch (\Exception $e) {
                                Log::instance()->warning('[Mutation Invite] Can not send an Invitation', [
                                    'error' => $e->getMessage(),
                                ]);

                                return;
                            }
                        }
                    ],

                    'join' => [
                        'type' => Types::collaborator(),
                        'description' => 'Sync Collaborator',
                        'args' => [
                            'userId' => Type::nonNull(Type::id()),
                            'token' => Type::nonNull(Type::string()),
                            'ownerId' => Type::nonNull(Type::id()),
                            'folderId' => Type::nonNull(Type::id())
                        ],
                        'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                            try {
                                if (!Auth::checkUserAccess($args['userId'])) {
                                    throw new AuthException('Access denied');
                                }

                                /**
                                 * Add a Collaborator to the Shared Folder
                                 */
                                $originalFolder = new Folder($args['ownerId'], $args['folderId']);

                                if (!$originalFolder->ownerId || !$originalFolder->id) {
                                    throw new CollaboratorException('Folder does not exist');
                                }

                                $collaborator = new Collaborator($originalFolder, $args['token']);

                                if (!$collaborator->exists()) {
                                    throw new CollaboratorException('Collaborator does not exists');
                                }

                                $collaborator->sync($args);

                                /**
                                 * Accept an Invitation
                                 * Save Shared Folder to the Acceptor's Folders collection
                                 */
                                if (!empty($args['userId'])) {
                                    $collaborator->saveFolder($originalFolder);
                                }


                                $selectedFields = $info->getFieldSelection();
                                if (isset($selectedFields['user'])) {
                                    $collaborator->fillUser();
                                }

                                return $collaborator;
                            } catch (CollaboratorException $e) {
                                Log::instance()->warning('[Mutation Join] Can not proccess joining', [
                                    'error' => $e->getMessage(),
                                ]);

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

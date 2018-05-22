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
use App\Components\Notify\Notify;
use App\Schema\Types;
use App\System\Config;
use App\System\Log;
use GraphQL\Type\Definition\{
    ObjectType,
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
                            'id' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => 'Unique identifier'
                            ],
                            'name' => [
                                'type' => Type::nonNull(Type::string()),
                                'description' => 'Full name'
                            ],
                            'email' => [
                                'type' => Type::nonNull(Type::string()),
                                'description' => 'Email address'
                            ],
                            'photo' => [
                                'type' => Type::string(),
                                'description' => 'Photo URL'
                            ],
                            'dtModify' => [
                                'type' => Type::int(),
                                'description' => 'Timestamp of last modification'
                            ]
                        ],
                        'resolve' => function ($root, $args) {
                            if (!Auth::checkUserAccess($args['id'])) {
                                throw new AuthException('Access denied');
                            }

                            $user = new User();
                            $user->sync($args);

                            return $user;
                        }
                    ],

                    'folder' => [
                        'type' => Types::folder(),
                        'description' => 'Sync Folder',
                        'args' => [
                            'id' => [
                                'description' => 'Folder\'s id',
                                'type' => Type::nonNull(Type::id()),
                            ],
                            'ownerId' => [
                                'description' => 'Owner\'s id',
                                'type' => Type::nonNull(Type::id()),
                            ],
                            'title' => [
                                'description' => 'Folder\'s title',
                                'type' => Type::nonNull(Type::string())
                            ],
                            'dtCreate' => [
                                'description' => 'Folder\'s date created',
                                'type' => Type::int()
                            ],
                            'dtModify' => [
                                'description' => 'Folder\'s date modified',
                                'type' => Type::int()
                            ],
                            'isShared' => [
                                'description' => 'Is this Folder a Shared Folder (link to another one Folder)',
                                'type' => Type::boolean()
                            ],
                            'isRemoved' => [
                                'description' => 'Is this Folder removed',
                                'type' => Type::boolean()
                            ],
                            'isRoot' => [
                                'description' => 'Is this Folder a User\'s Root Folder',
                                'type' => Type::boolean()
                            ],
                        ],
                        'resolve' => function ($root, $args) {
                            Log::instance()->debug('FOLDER MUTATION');
                            /**
                             * Get real Folder
                             */
                            $folder = new Folder($args['ownerId'], $args['id']);

                            /** Check access to this Folder */
                            $isFolderOwner = Auth::checkUserAccess($folder->ownerId);
                            if (!$isFolderOwner) {
                                $isCollaborator = $folder->hasUserAccess(Auth::userId());
                                if (!$isCollaborator) {
                                    throw new AuthException('Access denied');
                                }
                            }

                            /**
                             * Do not save old data
                             */
                            if ($folder->dtModify < $args['dtModify'] || Config::get('IGNORE_DTMODIFY_IN_MUTATIONS')) {
                                /** Set event name */
                                $eventName = Notify::FOLDER_UPDATE;
                                if ($folder->title != $args['title']) {
                                    $eventName = Notify::FOLDER_RENAME;
                                }
                                $folder->sync($args);

                                /** Send notifies only in folder was renamed case */
                                if ($eventName == Notify::FOLDER_RENAME) {
                                    /** Send notifies */
                                    $sender = Auth::getUser();
                                    $folder->notifyCollaborators(Notify::FOLDER_UPDATE, $folder, $sender);
                                }
                            } else {
                                Log::instance()->debug(`[Folder Mutation]: do not run folder->sync cause dtModify {$args['dtModify']} is not greater than saved note's dtModify {$folder->dtModify} in DB`);
                            }

                            return $folder;
                        }
                    ],

                    'note' => [
                        'type' => Types::note(),
                        'description' => 'Sync Note',
                        'args' => [
                            'id' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => ''
                            ],
                            'authorId' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => ''
                            ],
                            'folderId' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => ''
                            ],
                            'title' => [
                                'type' => Type::nonNull(Type::string()),
                                'description' => ''
                            ],
                            'content' => [
                                'type' => Type::nonNull(Type::string()),
                                'description' => ''
                            ],
                            'dtCreate' => [
                                'type' => Type::int(),
                                'description' => ''
                            ],
                            'dtModify' => [
                                'type' => Type::int(),
                                'description' => ''
                            ],
                            'isRemoved' => [
                                'type' => Type::boolean(),
                                'description' => ''
                            ],
                        ],
                        'resolve' => function ($root, $args) {
                            if (!Auth::checkUserAccess($args['authorId'])) {
                                throw new AuthException('Access denied');
                            }

                            /**
                             * Get target Folder
                             *
                             * We need to get Folder's Owner.
                             * If this Folder is Shared, we'll get a real Owner to get right collection
                             */
                            $folder = new Folder($args['authorId'],
                                $args['folderId']);

                            if (is_null($folder->id)) {
                                throw new NoteException('Incorrect Folder passed');
                            }

                            $note = new Note($folder->ownerId, $folder->id, $args['id']);

                            /**
                             * Do not save old data
                             */
                            if ($note->dtModify < $args['dtModify'] || Config::get('IGNORE_DTMODIFY_IN_MUTATIONS')) {
                                $note->sync($args);

                                /** Send notifies */
                                $sender = Auth::getUser();
                                $folder->notifyCollaborators(Notify::NOTE_UPDATE,
                                    $note, $sender);
                            } else {
                                Log::instance()->debug(`[Note Mutation]: do not run note->sync cause dtModify {$args['dtModify']} is not greater than saved note's dtModify {$note->dtModify} in DB`);
                            }

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

                            /** Send notifies */
                            $sender = Auth::getUser();
                            $originalFolder->notifyCollaborators(Notify::COLLABORATOR_INVITE, $collaborator, $sender);

                            return $collaborator;
                        }
                    ],

                    'join' => [
                        'type' => Types::collaborator(),
                        'description' => 'Sync Collaborator',
                        'args' => [
                            'userId' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => ''
                            ],
                            'token' => [
                                'type' => Type::nonNull(Type::string()),
                                'description' => ''
                            ],
                            'ownerId' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => ''
                            ],
                            'folderId' => [
                                'type' => Type::nonNull(Type::id()),
                                'description' => ''
                            ],
                        ],
                        'resolve' => function ($root, $args) {
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

                            if ($collaborator->userId) {
                                throw new CollaboratorException('This join link has already been used');
                            }

                            $collaborator->sync($args);

                            /**
                             * Accept an Invitation
                             * Save Shared Folder to the Acceptor's Folders collection
                             */
                            if (!empty($args['userId'])) {
                                $collaborator->saveFolder($originalFolder);
                            }

                            /** Send notifies */
                            $sender = Auth::getUser();
                            $originalFolder->notifyCollaborators(Notify::COLLABORATOR_JOIN, $collaborator, $sender);

                            return $collaborator;
                        }
                    ]
                ];
            }
        ];

        parent::__construct($config);
    }
}

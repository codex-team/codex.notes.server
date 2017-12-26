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
    Folder
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
                        'description' => 'Sync folder',
                        'args' => [
                            'id'    => Type::nonNull(Type::id()),
                            'name'  => Type::nonNull(Type::string()),
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
                        'description' => 'Sync folder',
                        'args' => [
                            'id'        => Type::nonNull(Type::id()),
                            'ownerId'   => Type::nonNull(Type::id()),
                            'title'     => Type::nonNull(Type::string()),
                            'dtCreate'  => Type::int(),
                            'dtModify'  => Type::int(),
                            'isShared'  => Type::boolean(),
                            'isRemoved' => Type::boolean()
                        ],
                        'resolve' => function($root, $args, $context, ResolveInfo $info) {

                            $folder = new Folder($args['ownerId']);
                            $folder->sync($args);

                            $selectedFields = $info->getFieldSelection();

                            if (in_array('notes', $selectedFields)) {
                                $folder->fillNotes();
                            }

                            if (in_array('owner', $selectedFields)) {
                                $folder->fillOwner();
                            }

                            return $folder;
                        }
                    ],

                    'note' => [
                        'type' => Types::folder(),
                        'description' => 'Sync folder',
                        'args' => [
                            'id'        => Type::nonNull(Type::id()),
                            'authorId'  => Type::nonNull(Type::id()),
                            'folderId'  => Type::nonNull(Type::id()),
                            'title'     => Type::nonNull(Type::string()),
                            'content'   => Type::nonNull(Type::string()),
                            'dtCreate'  => Type::int(),
                            'dtModify'  => Type::int(),
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

                ];
            }
        ];

        parent::__construct($config);
    }
}

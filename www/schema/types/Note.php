<?php

namespace App\Schema\Types;

use App\Components\Api\Models as Models;
use App\Schema\Types;
use GraphQL\Type\Definition\{
    ObjectType,
    Type
};

/**
 * Note type
 *
 * @package App\Schema\Types
 */
class Note extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'NoteType',
            'description' => 'Note\'s data',
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
                    'content' => [
                        'type' => Type::string(),
                        'description' => 'Content in the JSON-format',
                    ],
                    'dtCreate' => [
                        'type' => Type::int(),
                        'description' => 'Creation timestamp',
                    ],
                    'dtModify' => [
                        'type' => Type::int(),
                        'description' => 'Last modification timestamp',
                    ],
                    'author' => [
                        'type' => Types::user(),
                        'description' => 'User who owens Folder with Note',
                        'resolve' => function ($note, $args) {
                            /**
                             * Create an empty Note model based on author and folder
                             */
                            $noteModel = new Models\Note($note->authorId, $note->folderId);

                            /** Get Note's author */
                            $noteModel->fillAuthor();

                            return $noteModel->author;
                        }
                    ],
                    'isRemoved' => [
                        'type' => Type::boolean(),
                        'description' => 'Removed status: true if Note marked as removed',
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}

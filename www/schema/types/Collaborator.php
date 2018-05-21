<?php

namespace App\Schema\Types;

use App\Components\Api\Models as Models;
use App\Schema\Types;
use GraphQL\Type\Definition\{
    ObjectType,
    Type
};

/**
 * Collaborator type
 *
 * @package App\Schema\Types
 */
class Collaborator extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'CollaboratorType',
            'description' => 'Collaborator\'s data',
            'fields' => function () {
                return [
                    'token' => [
                        'type' => Type::string(),
                        'description' => 'Invitation Token',
                    ],
                    'id' => [
                        'type' => Type::id(),
                        'description' => 'Unique identifier',
                    ],
                    'email' => [
                        'type' => Type::string(),
                        'description' => 'Email address',
                    ],
                    'folder' => [
                        'type' => Types::folder(),
                        'description' => 'Shared Folder'
                    ],
                    'user' => [
                        'type' => Types::user(),
                        'description' => 'Invitation acceptor user. Appears after invite acceptance.',
                        'resolve' => function ($collaborator, $args) {
                            $userModel = new Models\User($collaborator->userId);

                            return $userModel;
                        }
                    ],
                    'dtInvite' => [
                        'type' => Type::int(),
                        'description' => 'Date of an invitation sending',
                    ],
                    'isRemoved' => [
                        'type' => Type::boolean(),
                        'description' => 'Removed status: true if Collaborator marked as removed',
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}

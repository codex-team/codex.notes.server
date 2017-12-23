<?php

namespace App\Schema\Types;

use GraphQL\Type\Definition\{
    ObjectType,
    Type
};
use App\Schema\Types;

/**
 * Collaborator type
 * @package App\Schema\Types
 */
class Collaborator extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function() {
                return [
                    'id' => [
                        'type' => Type::id(),
                        'description' => 'Collaborator\'s unique identifier',
                    ],
                    'email' => [
                        'type' => Type::string(),
                        'description' => 'Collaborator\'s email address',
                    ],
                    'user' => [
                        'type' => Types::user(),
                        'description' => 'Invitation acceptor user. Appears after invite acceptance.',
                    ],
                    'invitation_token' => [
                        'type' => Type::string(),
                        'description' => 'Token with <owner_id>:<folder_id>:<hash>',
                    ],
                    'dt_invite' => [
                        'type' => Type::int(),
                        'description' => 'Date of an invitation sending',
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}
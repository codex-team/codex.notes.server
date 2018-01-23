<?php

namespace App\Schema\Types;

use App\Schema\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

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
            'fields' => function () {
                return [
                    'token' => [
                        'type' => Type::string(),
                        'description' => 'Collaborator\'s Invitation Token',
                    ],
                    'id' => [
                        'type' => Type::id(),
                        'description' => 'Collaborator User\'s id',
                    ],
                    'email' => [
                        'type' => Type::string(),
                        'description' => 'Collaborator\'s email address',
                    ],
                    'user' => [
                        'type' => Types::user(),
                        'description' => 'Invitation acceptor user. Appears after invite acceptance.',
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

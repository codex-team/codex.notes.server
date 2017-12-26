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
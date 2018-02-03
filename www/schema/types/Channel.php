<?php

namespace App\Schema\Types;

use GraphQL\Type\Definition\{
    ObjectType,
    Type
};

/**
 * Channel type
 *
 * @package App\Schema\Types
 */
class Channel extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::id(),
                        'description' => 'Channel\'s unique identifier',
                    ]
                ];
            }
        ];

        parent::__construct($config);
    }
}

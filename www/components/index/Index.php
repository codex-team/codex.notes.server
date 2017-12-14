<?php

namespace App\Components\Index;

use App\System\Renderer;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Index page component
 */
class Index
{
    /**
     * Index page action
     */
    public function page()
    {
        // Renderer::render('index.php', [ 'title' => 'CodeX Notes' ]);

        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'echo' => [
                    'type' => Type::string(),
                    'args' => [
                        'message' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => function ($root, $args) {
                        return $root['prefix'] . $args['message'];
                    }
                ],
            ],
        ]);

        dump($queryType);
    }

}
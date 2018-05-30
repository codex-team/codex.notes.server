<?php

namespace App\Components\Index;

use App\Components\Base\Models\Exceptions\RenderException;
use App\System\Renderer;

/**
 * Index page component
 */
class Index
{
    /**
     * Index page action
     *
     * @param mixed $req
     * @param mixed $res
     *
     * @throws RenderException
     */
    public function page($req, $res)
    {
        $res->write(Renderer::render('index.php', ['title' => 'CodeX Notes']));
    }

    /**
     * Join page action
     *
     * @param mixed $req
     * @param mixed $res
     * @param mixed $args
     *
     * @throws RenderException
     */
    public function join($req, $res, $args)
    {
        $res->write(Renderer::render('join.php', [
            'title' => 'CodeX Notes',
            'ownerId' => $args['ownerId'],
            'folderId' => $args['folderId'],
            'token' => $args['token']
        ]));
    }
}

<?php

namespace App\Components\Index;

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
     */
    public function page($req, $res)
    {
        $res->write(Renderer::render('index.php', ['title' => 'CodeX Notes']));
    }
}

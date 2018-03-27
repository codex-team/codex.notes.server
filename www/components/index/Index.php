<?php

namespace App\Components\Index;

use App\Components\Api\Models\Folder;
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
        $folder = new Folder('5a9f8ef1c574153909750158', 'dAcuWSf90D3MYlpV');
        $folder->fillCollaborators();
        $folder->fillNotes();


        $out = json_encode($folder);

        echo var_dump($out);
        die();

        //$res->write(Renderer::render('index.php', ['title' => 'CodeX Notes']));
    }

    /**
     * Join page action
     *
     * @param mixed $req
     * @param mixed $res
     * @param mixed $args
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

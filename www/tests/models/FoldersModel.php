<?php

namespace App\Tests\Models;

use App\Components\Api\Models\Folder;

class FoldersModel extends Folder
{
    public function __construct(string $ownerId, string $id, array $folderData)
    {
        parent::__construct($ownerId, $id, $folderData);

        $data = [
            'id' => $id,
            'ownerId' => $ownerId,
        ];

        $data = array_merge($data, $folderData);

        $this->sync($data);
    }
}

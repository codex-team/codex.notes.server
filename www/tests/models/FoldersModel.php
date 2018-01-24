<?php

namespace App\Tests\Models;

use App\Components\Api\Models\Folder;

class FoldersModel extends Folder
{
    public function __construct($ownerId, $id, $title, $folderData)
    {
        parent::__construct($ownerId, $id, $folderData);

        $data = [
            'ownerId' => $ownerId,
            'id' => $id,
            'data' => $folderData,
            'title' => $title
        ];

        $this->sync($data);
    }

    public static function getCreateNewUserMutation($id, $ownerId, $title, $dtCreate, $dtModify, $isShared, $isRemoved)
    {
        return [
            'query' => 'mutation CreateNewFolder($id: ID!, $ownerId: ID!, $title: String!, $dtCreate: Int!, $dtModify:Int!, $isShared: Boolean!, $isRemoved: Boolean!) {
                          folder(id: $id, ownerId: $ownerId, title: $title, dtCreate: $dtCreate, dtModify: $dtModify, isShared: $isShared, isRemoved: $isRemoved) {
                            id,
                            owner {
                              id
                            },
                            title,
                            dtCreate,
                            dtModify,
                            isShared,
                            isRemoved
                          }
                        }',
            'variables' => [
                'id' => $id,
                'ownerId' => $ownerId,
                'title' => $title,
                'dtCreate' => $dtCreate,
                'dtModify' => $dtModify,
                'isShared' => $isShared,
                'isRemoved' => $isRemoved
            ],
            'operationName' => 'CreateNewUser'
        ];
    }

    public static function getFindFolderQuery($id, $ownerId)
    {
        return [
            'query' => 'query { folder (id:"' . $id . '", ownerId: "' . $ownerId . '") {
                            id,
                            title
                          }
                        }'
        ];
    }
}

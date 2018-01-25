<?php

namespace App\Tests\Models;

use App\Components\Api\Models\Folder;

class FoldersModel extends Folder
{
    public function __construct(string $ownerId, string $id, string $title, array $folderData)
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

    public static function getCreateNewUserMutation(string $id, string $ownerId, string $title, int $dtCreate, int $dtModify, bool $isShared, bool $isRemoved)
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

    public static function getFindFolderQuery(string $id, string $ownerId)
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
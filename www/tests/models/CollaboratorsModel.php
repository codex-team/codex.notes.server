<?php

namespace App\Tests\Models;

use App\Components\Api\Models\Collaborator;

class CollaboratorsModel extends Collaborator
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

    public static function getCreateNewCollaboratorMutation($userId, $ownerId, $folderId, $email, $dtInvite, $isRemoved, $token = null)
    {
        return [
            'query' => 'mutation Collaborator($userId: ID!, $ownerId: ID!, $folderId: ID!, $email: String!, $dtInvite:Int!, $isRemoved: Boolean!) {
                          collaborator(userId: $userId, ownerId: $ownerId, folderId: $folderId, email: $email, dtInvite: $dtInvite, isRemoved: $isRemoved) {
                            user {
                                id
                            },
                            email,
                            dtInvite,
                            isRemoved
                          }
                        }',
            'variables' => [
                'userId' => $userId,
                'ownerId' => $ownerId,
                'folderId' => $folderId,
                'email' => $email,
                'dtInvite' => $dtInvite,
                'isRemoved' => $isRemoved
            ],
            'operationName' => 'CreateNewUser'
        ];
    }
}

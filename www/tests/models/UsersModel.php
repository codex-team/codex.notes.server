<?php

namespace App\Tests\Models;

use App\Components\Api\Models\User;

class UsersModel extends User
{
    public function __construct(string $id, string $name, string $email, int $dtReg)
    {
        parent::__construct(null);

        $data = [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'dtReg' => $dtReg
        ];

        $this->sync($data);
    }

    public static function getCreateNewUserMutation(string $id, string $name, string $email, int $dtReg)
    {
        return [
            'query' => 'mutation CreateNewUser($id: ID!, $name: String!, $email: String!, $dtReg: Int!) {
                          user(id: $id, name: $name, email: $email, dtReg: $dtReg) {
                            id,
                            name,
                            email,
                            dtReg
                          }
                        }',
            'variables' => [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'dtReg' => $dtReg
            ],
            'operationName' => 'CreateNewUser'
        ];
    }

    public static function getFindUserQuery(string $id)
    {
        return [
            'query' => 'query { user(id:"' . $id . '") {
                            id
                          }
                        }'
        ];
    }
}

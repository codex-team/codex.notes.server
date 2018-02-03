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
}

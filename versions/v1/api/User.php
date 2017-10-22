<?php

namespace App\Versions\V1\Api;

use App\Versions\V1\Api;
use App\Versions\V1\Models\User as ModelUser;

class User extends Api
{
    protected $user;
    
    function __construct()
    {
        parent::__construct();

        $this->user = new ModelUser();
    }

    public function create($ip, $password)
    {
        return $this->user->create($ip, $password);
    }

    public function get($userId)
    {
        return  $this->user->get($userId);
    }
}
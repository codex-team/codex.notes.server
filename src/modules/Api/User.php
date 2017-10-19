<?php

namespace App\Modules\Api;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Modules\Api;

class User extends Api
{    
    /**
     * \Model\User;
     * @var [type]
     */
    protected $user;
    
    function __construct()
    {
        $this->user = new \App\Models\User();
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
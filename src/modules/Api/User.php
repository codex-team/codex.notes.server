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
    
    function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);

        $this->user = new \App\Models\User();
    }

    public function create()
    {
        $pass = $this->request->getParam('password');
        $ip   = $this->request->getAttribute('ip_address');

        $this->_response = $this->user->create($ip, $pass);
    }

    public function get()
    {
        $userId = $this->request->getAttribute('userId');

        $this->_response = $this->user->get($userId);
    }
}
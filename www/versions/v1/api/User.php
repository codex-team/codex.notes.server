<?php

namespace App\Versions\V1\Api;

use App\Versions\V1\Api;
use App\Versions\V1\Models\User as ModelUser;

/**
 * Class User
 * Объект API для работы с Моделью User
 *
 * @package App\Versions\V1\Api
 */
class User extends Api
{
    /**
     * Храним модель пользователя
     * @var ModelUser
     */
    protected $user;
    
    function __construct()
    {
        parent::__construct();

        $this->user = new ModelUser();
    }

    /**
     * Создаем нового пользователя
     *
     * @param $ip
     * @param $password
     *
     * @return array
     */
    public function create($ip, $password)
    {
        $this->response = $this->user->create($ip, $password);

        return $this;
    }

    /**
     * Получаем пользователя по id
     *
     * @param $userId
     *
     * @return null|object
     */
    public function get($userId)
    {
        $this->response = $this->user->get($userId);

        return $this;
    }
}
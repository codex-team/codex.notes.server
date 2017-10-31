<?php

namespace App\Versions\V1\Api;

use App\Versions\V1\Api;
use App\Versions\V1\Models\Exceptions\ModelException;
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
        return $this->user->create($ip, $password);
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
        return $this->user->get($userId);
    }
}
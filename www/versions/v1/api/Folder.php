<?php

namespace App\Versions\V1\Api;

use App\Versions\V1\Api;
use App\Versions\V1\Models\Folder as ModelFolder;

/**
 * Class User
 * Объект API для работы с Моделью User
 *
 * @package App\Versions\V1\Api
 */
class Folder extends Api
{
    /**
     * Храним модель папки
     * @var ModelFolder
     */
    protected $folder;
    
    function __construct()
    {
        parent::__construct();

        $this->folder = new ModelFolder();
    }

    /**
     * Создаем папку
     *
     * @param $user      User id
     * @param $name      Folder name
     * @param $id        Folder id
     * @param $timestamp unix time
     *
     * @return array
     */
    public function create($user, $name, $id, $timestamp)
    {
        $this->response = $this->folder->create($user, $name, $id, $timestamp);

        return $this;
    }

    /**
     * Получаем папку
     * @param $user User id
     * @param $id   Folder id
     *
     * @return null|object
     */
    public function delete($user, $id)
    {
        $this->response = $this->folder->delete($user, $id);

        return $this;
    }

    /**
     * Add collaborator to folder
     *
     * @param $user
     * @param $id
     * @param $email
     * @return $this
     */
    public function addCollaborator($user, $id, $email) {
        $this->response = $this->folder->addCollaborator($user, $id, $email);

        return $this;
    }

    /**
     * Verify collaborator
     *
     * @param string $userId
     * @param string $email
     * @param string $token
     * @return $this
     */
    public function verifyCollaborator($userId, $email, $token) {
        $this->response = $this->folder->verifyCollaborator($userId, $email, $token);

        return $this;
    }

}
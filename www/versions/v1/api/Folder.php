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
     * @param $user     User id
     * @param $name     Folder name
     *
     * @return array
     */
    public function create($user, $name)
    {
        $this->response = $this->folder->create($user, $name);

        return $this;
    }

    /**
     * Получаем папку
     *
     * @return null|object
     */
    public function delete($user, $name)
    {
        $this->response = $this->folder->delete($user, $name);

        return $this;
    }
}
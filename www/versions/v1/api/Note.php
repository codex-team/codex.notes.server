<?php

namespace App\Versions\V1\Api;

use App\Versions\V1\Api;
use App\Versions\V1\Models\Note as ModelNote;

/**
 * Class Note
 * Объект API для работы с Моделью Note
 *
 * @package App\Versions\V1\Api
 */
class Note extends Api
{
    /**
     * Храним модель папки
     * @var ModelNote
     */
    protected $note;
    
    function __construct()
    {
        parent::__construct();

        $this->note = new ModelNote();
    }

    /**
     * Создаем папку
     *
     * @param $user     User id
     * @param $name     Folder name
     *
     * @return array
     */
    public function create($dirId, $dirName, $timestamp)
    {
        $this->response = $this->note->create($dirId, $dirName, $timestamp);

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
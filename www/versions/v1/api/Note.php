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
     * Храним модель записи
     * @var ModelNote
     */
    protected $note;
    
    function __construct()
    {
        parent::__construct();

        $this->note = new ModelNote();
    }

    /**
     * Создаем заметку (in developing)
     *
     * @param $noteName   Note name
     * @param $noteId     Note id
     * @param $timestamp  unix time
     */
    public function create($noteId, $noteName, $timestamp)
    {
        $this->response = $this->note->create($noteId, $noteName, $timestamp);

        return $this;
    }

    /**
     * Получаем заметку (in developing)
     *
     * @param $user       User id
     * @param $noteId     Note id
     */
    public function delete($user, $noteId)
    {
        $this->response = $this->note->delete($user, $noteId);

        return $this;
    }
}
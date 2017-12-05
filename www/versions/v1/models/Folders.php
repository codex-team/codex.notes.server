<?php

namespace App\Versions\V1\Models;

use App\System\Utilities\Messages;
use APP\System\HTTP;
use App\Versions\V1\Models\Mongo;
use App\Versions\V1\Models\Exceptions\ControllerException;

/**
 * Class Folders
 * Модель для работы с коллекцией директорий в MongoDB
 *
 * @package App\Versions\V1\Models
 */
class Folders extends Base
{
    private $client;

    /**
     * Имя коллекции в Mongo
     * @var string
     */
    private $collectionName = 'folders';
    
    public function __construct() {

        $this->messages = Messages::load('v1', 'folder');

        $this->client = new Mongo();
    }

    /**
     * Создаем коллекцию папок и в нее вставляем запись о другой папке
     *
     * @param string $user      User id
     * @param string $name      Folder name
     * @param string $id        Folder id
     * @param int    $timestamp
     *
     * @return bool | exception
     * @throws ControllerException
     */
    public function create(string $user = '', string $name = '', string $id = '', int $timestamp = 0)
    {
        if (!$user) {
            throw new ControllerException($this->messages['user']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        if (!$name) {
            throw new ControllerException($this->messages['folder']['name']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        if (!$id) {
            throw new ControllerException($this->messages['folder']['id']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        $this->collectionName .= ':' . $user;

        if (!$this->client->collectionIsset($this->collectionName)) {
            $folderIdUid = $this->client->createCollection($this->collectionName);
        }

        $content = [
            'did' => $id,
            'title' => $name,
            'timestamp' => $timestamp
        ];

        $this->client->insert($this->collectionName, $content);

        return true;
    }

    /**
     * Удаляем коллекцию папок
     *
     * @param string $user  User id
     * @param string $id    Folder id
     *
     * @return bool | exception
     * @throws ControllerException
     */
    public function delete(string $user = '', string $id = '')
    {
        if (!$user) {
            throw new ControllerException($this->messages['user']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        if (!$id) {
            throw new ControllerException($this->messages['folder']['id']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        $this->collectionName .= ':' . $user;

        $this->client->deleteInCollection($this->collectionName, ['did' => $id]);

        return true;
    }
}
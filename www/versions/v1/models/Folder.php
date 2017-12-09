<?php

namespace App\Versions\V1\Models;

use App\System\Utilities\Messages;
use APP\System\HTTP;
use App\Versions\V1\Models\Mongo;
use App\Versions\V1\Models\Exceptions\ControllerException;

/**
 * Class Folder
 * Модель для работы с коллекцией директории в MongoDB
 *
 * @package App\Versions\V1\Models
 */
class Folder extends Base
{
    private $client;

    /**
     * Имя коллекции в Mongo
     * @var string
     */
    private $collectionName = 'folder';
    
    public function __construct()
    {
        parent::__construct();

        $this->messages = Messages::load('v1', 'folder');

        $this->client = new Mongo();
    }

    /**
     * Создаем коллекцию папок для пользователя и в нее вставляем новую папку
     *
     * @param string $user      User id
     * @param string $name      Folder name
     * @param string $id        Folder id
     * @param int    $timestamp unix time
     *
     * @return bool | exception
     * @throws ControllerException
     */
    public function create(string $user = '', string $name = '', string $id = '', int $timestamp = 0 )
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

        $folderIdUidCollectionName = $this->collectionName . ':' . $id . ':' . $user;

        $folderIdUid = $this->client->createCollection($folderIdUidCollectionName);

        $folders = new Folders();
        $folderUid = $folders->create($user, $name, $id, $timestamp);

        return true;
    }

    /**
     * Удаляем папку из коллекции папок пользователя
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

        $folderCollection = $this->collectionName .= ':' . $id . ':' . $user;

        $this->client->deleteCollection($folderCollection);

        $folders = new Folders();
        $foldersCollection = $folders->delete($user, $id);

        return true;
    }

    /**
     * Add collaborator to folder
     *
     * @param string $userId
     * @param string $folderId
     * @param $email
     * @return bool
     */
    public function addCollaborator(string $userId, string $folderId, $email)
    {

        $collaboratorsCollection = 'collaborators:' . $userId . ':' . $folderId;

        $this->client->insert($collaboratorsCollection, [
            'email' => $email,
            'accepted' => 0
        ]);

        return true;

    }

}
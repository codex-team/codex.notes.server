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
    
    public function __construct() {

        $this->messages = Messages::load('v1', 'folder');

        $this->client = new Mongo();
    }

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
}
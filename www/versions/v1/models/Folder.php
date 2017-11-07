<?php

namespace App\Versions\V1\Models;

use App\System\Utilities\Messages;
use APP\System\HTTP;
use App\Versions\V1\Models\Exceptions\DatabaseException;
use App\Versions\V1\Models\Mongo;
use App\Versions\V1\Models\Exceptions\ControllerException;
use App\Versions\V1\Models\Exceptions\ModelException;

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
     * @var object MongoDB\Collection
     */
    private $collection;

    /**
     * Имя коллекции в Mongo
     * @var string
     */
    private $collectionName = 'folder';
    
    public function __construct() {

        $this->messages = Messages::load('v1', 'folder');

        $this->client = new Mongo();
    }

    public function create(string $user = '', string $name = '')
    {
        if (!$user) {
            throw new ControllerException($this->messages['user']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        if (!$name) {
            throw new ControllerException($this->messages['folder']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        $this->collectionName .= ':' . $name . ':' . $user;

        return $this->client->createCollection($this->collectionName);
    }

    public function delete(string $user = '', string $name = '')
    {
        if (!$user) {
            throw new ControllerException($this->messages['user']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        if (!$name) {
            throw new ControllerException($this->messages['folder']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        $this->collectionName .= ':' . $name . ':' . $user;

        return $this->client->deleteCollection($this->collectionName);
    }
}
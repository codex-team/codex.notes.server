<?php

namespace App\Versions\V1\Models;

use App\System\Utilities\Messages;
use APP\System\HTTP;
use App\Versions\V1\Models\Exceptions\DatabaseException;
use App\Versions\V1\Models\Mongo;
use App\Versions\V1\Models\Exceptions\ControllerException;
use App\Versions\V1\Models\Exceptions\ModelException;

/**
 * Class Note
 * Модель для работы с коллекцией записей в MongoDB
 *
 * @package App\Versions\V1\Models
 */
class Note extends Base
{
    private $client;

    /**
     * Имя коллекции в Mongo
     * @var string
     */
    private $collectionName = 'note';
    
    public function __construct() {

        $this->messages = Messages::load('v1', 'note');

        $this->client = new Mongo();
    }

    public function create(string $dirId = '', string $dirName = '', integer $timestamp = 0)
    {
        if (!$dirId) {
            throw new ControllerException($this->messages['dir']['id']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        if (!$dirName) {
            throw new ControllerException($this->messages['dir']['name']['empty'], HTTP::CODE_BAD_REQUEST);
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
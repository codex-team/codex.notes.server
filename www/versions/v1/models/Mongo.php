<?php 

namespace App\Versions\V1\Models;

use App\System\Utilities\Config;
use App\System\Utilities\Messages;
use App\System\HTTP;
use App\Versions\V1\Models\Exceptions\DatabaseException;
use App\Versions\V1\Models\Exceptions\ModelException;

/**
 * Class Mongo
 * Модель для работы с БД Mongo
 *
 * @package App\Versions\V1\Models
 */
class Mongo extends Base
{
    /**
     * MongoDB\Client
     * @var object
     */
    private $client;

    /**
     * [$connection description]
     * @var object
     */
    private $connection;

    /**
     * Устанавливаем соединение с базой
     * @param string|null $domain 
     * @param string|null $port
     * @param string|null $dbname
     */
    function __construct(string $domain = null, string $port = null, string $dbname = null)
    {
        $this->config = Config::load('mongo');
        $this->messages = Messages::load('v1', 'mongo');

        $domain = is_null($domain) ? $this->config['domain']   : $domain;
        $port   = is_null($port)   ? $this->config['port']     : $port;
        $dbname = is_null($dbname) ? $this->config['database'] : $dbname;
        
        try {
            $this->client = new \MongoDB\Client(
                "mongodb://{$domain}:{$port}"
            );

            $this->connection = $this->client->$dbname;
        } catch (\Exception $e) {
            throw new DatabaseException($this->messages['init']['error'], HTTP::CODE_SERVER_ERROR);
        }
    }
    
    /**
     * Получаем коллекции базы по имени коллекции
     * @param  string $collection
     * @return MongoDB\Collection
     */
    public function getCollection(string $collection = '')
    {
        if (!$collection) {
            throw new ModelException($this->messages['collection']['name']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        try {
            return $this->connection->$collection;
            
        } catch (\Exception $e) {
            $message = sprintf($this->messages['collection']['get']['error'], $collection);

            throw new DatabaseException($message, HTTP::CODE_SERVER_ERROR);
        }
        
    }

    public function createCollection(string $collection = '', array $content = [])
    {
        if (!$collection) {
            throw new ModelException($this->messages['collection']['name']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        try {
            $result = $this->connection->command(["create" => $collection]);
            return true;
        }
        catch (\Exception $e) {

            return false;
            /*
            $message = sprintf($this->messages['collection']['create']['error'], $collection, $e->getMessage());

            throw new DatabaseException($message, HTTP::CODE_SERVER_ERROR);
            */
        }
    }

    public function deleteCollection(string $collection = '')
    {
        if (!$collection) {
            throw new ModelException($this->messages['collection']['name']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        try {
            $result = $this->connection->$collection->drop();
        }
        catch (\Exception $e) {
            $message = sprintf($this->messages['collection']['remove']['error'], $collection, $e->getMessage());

            throw new DatabaseException($message, HTTP::CODE_SERVER_ERROR);
        }

        /* query ok, but collection not found */
        if ($result->ok != 1) {
            $message = sprintf($this->messages['collection']['remove']['error'], $collection, $result->errmsg);

            throw new DatabaseException($message, HTTP::CODE_SERVER_ERROR);
        }

        return $result;
    }

    public function insert(string $collection = '', array $content = [])
    {
        if (!$collection) {
            throw new ModelException($this->messages['collection']['name']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        return $this->connection->$collection->insertOne($content);
    }

    public function collectionIsset(string $collection = '')
    {
        if (!$collection) {
            throw new ModelException($this->messages['collection']['name']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        try {
            $this->connection->$collection->findOne([]);

            return true;
        }
        catch (\Exception $e) {
            return false;
        }
    }

    public function deleteInCollection(string $collection = '', array $criteria = [])
    {
        return $this->connection->$collection->deleteOne($criteria);
    }
}
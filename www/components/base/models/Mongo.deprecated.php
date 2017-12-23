<?php

namespace App\Components\Base\Models;

use App\Components\Base\Models\Exceptions\DatabaseException;
use App\Components\Base\Models\Exceptions\ModelException;
use App\System\HTTP;

/**
 * Class Mongo
 * Модель для работы с БД Mongo
 *
 * @todo singleton
 */
class Mongo
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
     *
     * @param string|null $domain
     * @param string|null $port
     * @param string|null $dbname
     *
     * @throws DatabaseException
     */
    function __construct(string $domain = null, string $port = null, string $dbname = null)
    {
        $domain = isset($_SERVER['MONGO_HOST'])   ? $_SERVER['MONGO_HOST']   : 'localhost';
        $port   = isset($_SERVER['MONGO_PORT'])   ? $_SERVER['MONGO_PORT']   : 27017;
        $dbname = isset($_SERVER['MONGO_DBNAME']) ? $_SERVER['MONGO_DBNAME'] : 'notes';

        try {
            $this->client = new \MongoDB\Client(
                "mongodb://{$domain}:{$port}"
            );

            $this->connection = $this->client->$dbname;
        } catch (\Exception $e) {
            throw new DatabaseException('Mongo int connection faulted', HTTP::CODE_SERVER_ERROR);
        }
    }

    /**
     * Получаем коллекции базы по имени коллекции
     *
     * @param  string $collection
     * @return MongoDB\Collection
     *
     * @throws ModelException
     * @throws DatabaseException
     */
    public function getCollection(string $collection = '')
    {
        if (!$collection) {
            throw new ModelException('Collection name is empty', HTTP::CODE_BAD_REQUEST);
        }

        try {
            return $this->connection->$collection;

        } catch (\Exception $e) {
            $message = sprintf('Collection %s is not received', $collection);

            throw new DatabaseException($message, HTTP::CODE_SERVER_ERROR);
        }

    }

    /**
     * Создаем коллекцию по ее имени
     *
     * @param string $collection    example: what:a:fuck
     *
     * @return bool
     * @throws ModelException
     */
    public function createCollection(string $collection = '', array $content = [])
    {
        if (!$collection) {
            throw new ModelException('Collection name is empty', HTTP::CODE_BAD_REQUEST);
        }

        try {
            $result = $this->connection->command(["create" => $collection]);
            return true;
        } catch (\Exception $e) {

            return false;
        }
    }

    /**
     * Удаляем коллекцию по ее имени
     *
     * @param string $collection
     *
     * @return array|object
     * @throws DatabaseException
     * @throws ModelException
     */
    public function deleteCollection(string $collection = '')
    {
        if (!$collection) {
            throw new ModelException('Collection name is empty', HTTP::CODE_BAD_REQUEST);
        }

        try {
            $result = $this->connection->$collection->drop();
        } catch (\Exception $e) {
            $message = sprintf('Collection %s is not removed: %s', $collection, $e->getMessage());

            throw new DatabaseException($message, HTTP::CODE_SERVER_ERROR);
        }

        /* query ok, but collection not found */
        if ($result->ok != 1) {
            $message = sprintf('Collection %s is not removed: %s', $collection, $result->errmsg);

            throw new DatabaseException($message, HTTP::CODE_SERVER_ERROR);
        }

        return $result;
    }

    /**
     * Вставляем данные в коллекцию
     *
     * @param string $collection
     * @param array  $content
     *
     * @return \MongoDB\InsertOneResult
     * @throws ModelException
     */
    public function insert(string $collection = '', array $content = [])
    {
        if (!$collection) {
            throw new ModelException('Collection name is empty', HTTP::CODE_BAD_REQUEST);
        }

        return $this->connection->$collection->insertOne($content);
    }

    /**
     * Обновляем данные в коллекции
     *
     * @param string $collection
     * @param array  $filter
     * @param array  $content
     * @param array  $options
     *
     * @return \MongoDB\UpdateResult
     * @throws ModelException
     */
    public function update(string $collection = '', array $filter = [], array $content = [], array $options = [])
    {
        if (!$collection) {
            throw new ModelException('Collection name is empty', HTTP::CODE_BAD_REQUEST);
        }

        return $this->connection->$collection->updateOne($filter, $content, $options);
    }

    /**
     * Удаляем данные из коллекции
     *
     * @param string $collection
     * @param array  $filter
     * @param array  $options
     *
     * @return \MongoDB\UpdateResult
     * @throws ModelException
     */
    public function remove(string $collection = '', array $filter = [], array $options = [])
    {
        if (!$collection) {
            throw new ModelException('Collection name is empty', HTTP::CODE_BAD_REQUEST);
        }

        return $this->connection->$collection->findOneAndDelete($filter, $options);
    }

    /**
     * Находим данные в коллекции
     *
     * @param string $collection
     * @param array  $filter
     *
     * @returns array
     * @throws ModelException
     */
    public function find(string $collection = '', array $filter = [])
    {
        if (!$collection) {
            throw new ModelException('Collection name is empty', HTTP::CODE_BAD_REQUEST);
        }

        return $this->connection->$collection->find($filter)->toArray();
    }






    public function findAndModify(string $collection, array $query = [], array $update = [], array $fields = [], array $options = [])
    {
        return $this->connection->$collection->findAndModify($query, $update, $fields, $options)->toArray();
    }



    /**
     * Проверяем существование коллекции
     *
     * @param string $collection
     *
     * @return bool
     * @throws ModelException
     */
    public function collectionIsset(string $collection = '')
    {
        if (!$collection) {
            throw new ModelException('Collection name is empty', HTTP::CODE_BAD_REQUEST);
        }

        try {
            $this->connection->$collection->findOne([]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Удаляем из коллекции что-то по условию
     *
     * @param string $collection
     * @param array  $criteria      example: ['did' => '126']
     *
     * @return \MongoDB\DeleteResult
     */
    public function deleteInCollection(string $collection = '', array $criteria = [])
    {
        return $this->connection->$collection->deleteOne($criteria);
    }
}
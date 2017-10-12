<?php 

namespace Modules;

use Modules\Mongo;

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
     * @param string|null $domain 
     * @param string|null $port
     * @param string|null $dbname
     */
    function __construct(string $domain = null, string $port = null, string $dbname = null)
    {
        global $config;

        $domain = (is_null($domain)) ? $config['mongodb']['domain'] : $domain;
        $port   = (is_null($port))   ? $config['mongodb']['port']   : $port;
        $dbname = (is_null($dbname)) ? $config['mongodb']['dbname'] : $dbname;
        
        try {
            $this->client = new \MongoDB\Client(
                "mongodb://{$domain}:{$port}"
            );

            $this->connection = $this->client->$dbname;
        } catch (Exception $e) {
            
        }
    }

    /**
     * Получаем коллекции базы по имени коллекции
     * @param  string $collection
     * @return MongoDB\Collection
     */
    public function getCollection(string $collection = '')
    {
        return $this->connection->$collection;
    }
}
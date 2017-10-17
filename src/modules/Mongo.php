<?php 

namespace App\Modules;

/**
 * @method collection getCollection()
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
     * @param string|null $domain 
     * @param string|null $port
     * @param string|null $dbname
     */
    function __construct(string $domain = null, string $port = null, string $dbname = null)
    {
        global $config, $logger;

        $domain = is_null($domain) ? $config['mongodb']['domain'] : $domain;
        $port   = is_null($port)   ? $config['mongodb']['port']   : $port;
        $dbname = is_null($dbname) ? $config['mongodb']['dbname'] : $dbname;
        
        try {
            $this->client = new \MongoDB\Client(
                "mongodb://{$domain}:{$port}"
            );

            $this->connection = $this->client->$dbname;
        } catch (Exception $e) {
            $message = $messages['mongo']['init']['error']; 
            $logger->error($message, [$e->getMessage()]);
        }
    }
    
    /**
     * Получаем коллекции базы по имени коллекции
     * @param  string $collection
     * @return MongoDB\Collection
     */
    public function getCollection(string $collection = '')
    {
        global $logger, $messages;

        try {
            return $this->connection->$collection;
            
        } catch (Exception $e) {
            $message = sprintf($messages['mongo']['collection']['get']['error'], $collection);
            
            $logger->error($message, [$e->getMessage()]);
        }
        
    }
}
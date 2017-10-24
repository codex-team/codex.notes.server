<?php 

namespace App\Versions\V1\Models;

use App\System\Utilities\Config;
use App\System\Utilities\Message;

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
        $this->messages = Message::load('v1', 'user');

        $domain = is_null($domain) ? $this->config['domain']   : $domain;
        $port   = is_null($port)   ? $this->config['port']     : $port;
        $dbname = is_null($dbname) ? $this->config['database'] : $dbname;
        
        try {
            $this->client = new \MongoDB\Client(
                "mongodb://{$domain}:{$port}"
            );

            $this->connection = $this->client->$dbname;
        } catch (Exception $e) {
            $logger->error($this->messages['init']['error'], [$e->getMessage()]);
        }
    }
    
    /**
     * Получаем коллекции базы по имени коллекции
     * @param  string $collection
     * @return MongoDB\Collection
     */
    public function getCollection(string $collection = '')
    {
        try {
            return $this->connection->$collection;
            
        } catch (Exception $e) {
            $message = sprintf($this->messages['collection']['get']['error'], $collection);

            $this->logger->error($message, [$e->getMessage()]);
        }
        
    }
}
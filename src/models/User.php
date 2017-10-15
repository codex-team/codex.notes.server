<?php

namespace App\Models;

use App\Modules\Mongo;
use App\Modules\Auth;

/**
 * @method string|array create()    
 * @method string|array get()
 * @method boolean      validate()  
 */
class User
{
    /**
     * Коллекция пользователей
     * @var object MongoDB\Collection
     */
    private $collection;

    /**
     * Коллекция пользователя
     * @var object null|MongoDB\Model\BSONDocument
     */
    private $collectionItem = NULL;

    /**
     * Имя коллекции в Mongo
     * @var string
     */
    private $collectionName = 'user';

    /**
     * Инициализация коллекции пользователей
     * @param string    $userId        Если указан, то также инициализируется
     *                                 коллекция пользователя @var $this->collectionItem
     */
    public function __construct(string $userId = '')
    {
        $client = new Mongo();
        
        $this->collection = $client->getCollection(
            $this->collectionName
        );

        if ($userId) {
            try {
                $user = $this->get($userId);

                if ($user) {
                    $this->collectionItem = $user;
                }

            } catch (Exception $e) {
                # user init fault
            }
        }
    }

    /**
     * Create user
     * @param  string           $ip
     * @param  string           $password
     * @return string|array     $result      'error text' or 
     *                                       ['id' => $userId, 'password' => $passwordHashed]
     */
    public function create(string $ip = '0.0.0.0', string $password = '')
    {
        global $logger, $messages;

        if (!$password) {
            $logger->error("{$ip} - {$messages['auth']['password']['empty']}");
            return $messages['auth']['password']['empty'];
        }

        $userId = Auth::generatePassword();
        $passwordHashed = Auth::generateHash($password);

        try {
            $result = $this->collection->insertOne([
                'id' => $userId,
                'password' => $passwordHashed,
                'ip' => $ip,
                'directories' => []
            ]);
            
            # {"$oid":"59dd20985d945f103933fd7e"}
            # $resultId = $result->getInsertedId()
            
            return [
                'id' => $userId, 
                'password' => $passwordHashed
            ];

        } catch (Exception $e) {
            $logger->error($messages['api']['user']['create']['error'], [$e->getMessage()]);
        }
    }

    /**
     * Get user collection by userId
     * @param  string         $userId    hex 'c305ed6c'
     * @return object|null    $result    MongoDB\Model\BSONDocument
     *   {
     *       _id":{
     *           "$oid":"59e396..ca3"
     *       },
     *       "id":"580d7602",
     *       "password":{
     *           "hash":"8e8d860..5d",
     *           "localSalt":"6ed..07f"
     *       },
     *       "ip":"::1",
     *       "directories":{}
     *   }
     */
    public function get(string $userId = '')
    {
        global $messages;

        if (!$userId) {
            return $messages['auth']['userId']['empty'];
        }

        try {
            $result = $this->collection->findOne([
                'id' => $userId   
            ]);

            return $result;

        } catch (Exception $e) {
            $logger->error($messages['api']['user']['get']['error'], [$e->getMessage()]);
        }
    }

    /**
     * Проверяем пароль пользователя
     * 
     * @param  array     $user
     * @param  string    $password
     * @return boolean
     */
    public function validate(array $user = [], string $password = '')
    {
        try {
            $checkHash = Auth::generateHash($password, $user['password']['localSalt']);

            return $user['password']['hash'] === $checkHash['hash'];
        } catch (Exception $e) {
            $logger->error($messages['api']['user']['validate']['error'], [$e->getMessage()]);
        }
    }    
}
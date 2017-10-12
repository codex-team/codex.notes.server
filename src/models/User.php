<?php

namespace Models;

require_once '../modules/Mongo.php';
require_once '../modules/Auth.php';

use Models\User;
use Modules\Mongo;
use Modules\Auth;

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
     * Создаем пользователя
     * @param  string    $ip
     * @param  string    $password
     * @return array     ['uid' => 'value']
     */
    public function create(string $ip = '0.0.0.0', string $password = '')
    {
        $userId = Auth::generatePassword();
        $passwordHashed = Auth::generateHash($password);

        try {
            $result = $this->collection->insertOne([
                'user_id' => $userId,
                'password' => $passwordHashed,
                'ip' => $ip,
                'directories' => []
            ]);
            
            # {"$oid":"59dd20985d945f103933fd7e"}
            # $resultId = $result->getInsertedId()
            
            return ['uid' => $userId];

        } catch (Exception $e) {

        }
    }

    /**
     * Получаем коллекцию пользователя по $userId
     * @param  string         $userId    hex строка 'c305ed6c'
     * @return object|null    $result    MongoDB\Model\BSONDocument
     */
    public function get(string $userId = '')
    {
        try {
            $result = $this->collection->findOne([
                'user_id' => $userId   
            ]);

            return $result;
        } catch (Exception $e) {
            
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
            
        }
    }    
}
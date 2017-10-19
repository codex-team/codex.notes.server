<?php

namespace App\Models;

use App\Modules\Mongo;
use App\Modules\Auth;
use App\Modules\HTTP;

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
        global $logger, $messages;

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
                $logger->error($messages['user']['init']['fault'], [$e->getMessage()]);
            }
        }
    }

    /**
     * Create user
     * @param  string   $ip
     * @param  string   $password
     * @return array    $result
     */
    public function create(string $ip = '0.0.0.0', string $password = '')
    {
        global $logger, $messages;

        if (!$password) {

            $message = $messages['auth']['password']['empty'];

            return [
                'code' => HTTP::CODE_BAD_REQUEST,
                'success' => FALSE,
                'result' => $message
            ];
        }

        $userId = Auth::generateHex();
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
                'code' => HTTP::CODE_SUCCESS,
                'success' => TRUE,
                'result' => [
                    'id' => $userId, 
                    'password' => $passwordHashed
                ]
            ];

        } catch (Exception $e) {

            $message = $messages['api']['user']['create']['error'];

            $logger->error($message, [$e->getMessage()]);

            return [
                'code' => HTTP::CODE_SERVER_ERROR,
                'success' => FALSE,
                'result' => $message,
            ];            
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
        global $config, $messages, $logger;

        $userIdLength = strlen($userId);
        $userIdLengthDefault = $config['auth']['passLen'];

        if (!$userIdLength) {
            $logger->error($messages['auth']['userId']['empty']);

            $message = $messages['auth']['userId']['empty'];

            return [
                'code' => HTTP::CODE_BAD_REQUEST,
                'success' => FALSE,
                'result' => $message
            ];  
        }
        elseif($userIdLength != $userIdLengthDefault) {

            $message = sprintf($messages['auth']['userId']['length'], $userIdLengthDefault);

            $logger->error($message);

            return [
                'code' => HTTP::CODE_BAD_REQUEST,
                'success' => FALSE,
                'result' => $message
            ];  
        }

        try {
            $result = $this->collection->findOne([
                'id' => $userId   
            ]);

            return [
                'code' => HTTP::CODE_SUCCESS,
                'success' => TRUE,
                'result' => $result
            ];  

        } catch (Exception $e) {

            $message = $messages['api']['user']['get']['error'];

            $logger->error($message, [$e->getMessage()]);

            return [
                'code' => HTTP::CODE_SERVER_ERROR,
                'success' => FALSE,
                'result' => $message
            ];
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
        global $logger;

        try {
            $checkHash = Auth::generateHash($password, $user['password']['localSalt']);

            $result = $user['password']['hash'] === $checkHash['hash'];

            return [
                'code' => HTTP::CODE_SERVER_ERROR,
                'success' => FALSE,
                'result' => $result
            ];

        } catch (Exception $e) {

            $message = $messages['api']['user']['validate']['error'];

            $logger->error($message, [$e->getMessage()]);

            return [
                'code' => HTTP::CODE_SERVER_ERROR,
                'success' => FALSE,
                'result' => $message
            ];
        }
    }    
}
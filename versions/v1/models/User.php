<?php

namespace App\Versions\V1\Models;

use App\System\Utilities\Messages;
use App\Versions\V1\Models\Mongo;
use App\Versions\V1\Utilities\Tweaks;
use App\System\HTTP;
use App\System\Utilities\Config;
use App\System\Utilities\Message;

/**
 * Class User
 * Объект для работы с коллекцией пользователя в Mongo
 *
 * @package App\Versions\V1\Models
 */
class User extends Base
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
    private $collectionItem = null;

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
        parent::__construct();

        $this->config = Config::load('user');
        $this->messages = Message::load('v1', 'user');

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
                $this->logger->error($this->messages['id']['empty'], [$e->getMessage()]);
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
        if (!$password) {

            return [
                'code' => HTTP::CODE_BAD_REQUEST,
                'success' => false,
                'result' => $this->messages['password']['empty']
            ];
        }

        $userId = Tweaks::generateHex();
        $passwordHashed = Tweaks::generateHash($password);

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
                'success' => true,
                'result' => [
                    'id' => $userId, 
                    'password' => $passwordHashed
                ]
            ];

        } catch (Exception $e) {

            $message = $this->messages['create']['error'];

            $this->logger->error($message, [$e->getMessage()]);

            return [
                'code' => HTTP::CODE_SERVER_ERROR,
                'success' => false,
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
        $userIdLength = strlen($userId);
        $userIdLengthDefault = $this->config['auth']['password']['length'];

        if (!$userIdLength) {
            $message = $this->messages['id']['empty'];

            $this->logger->error($message);

            return [
                'code' => HTTP::CODE_BAD_REQUEST,
                'success' => false,
                'result' => $message
            ];  
        }
        elseif($userIdLength != $userIdLengthDefault) {
            $message = sprintf($this->messages['id']['length'], $userIdLengthDefault);

            $this->logger->error($message);

            return [
                'code' => HTTP::CODE_BAD_REQUEST,
                'success' => false,
                'result' => $message
            ];  
        }

        try {
            $result = $this->collection->findOne([
                'id' => $userId   
            ]);

            return [
                'code' => HTTP::CODE_SUCCESS,
                'success' => true,
                'result' => $result
            ];  

        } catch (Exception $e) {

            $message = $this->messages['isset']['notIsset'];

            $this->logger->error($message, [$e->getMessage()]);

            return [
                'code' => HTTP::CODE_SERVER_ERROR,
                'success' => true,
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
        try {
            $checkHash = Tweaks::generateHash($password, $user['password']['localSalt']);

            $result = $user['password']['hash'] === $checkHash['hash'];

            return [
                'code' => HTTP::CODE_SERVER_ERROR,
                'success' => false,
                'result' => $result
            ];

        } catch (Exception $e) {

            $message = $this->messages['validate']['not'];

            $this->logger->error($message, [$e->getMessage()]);

            return [
                'code' => HTTP::CODE_SERVER_ERROR,
                'success' => false,
                'result' => $message
            ];
        }
    }    
}
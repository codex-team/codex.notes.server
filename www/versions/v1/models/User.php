<?php

namespace App\Versions\V1\Models;

use App\Versions\V1\Models\Exceptions\DatabaseException;
use App\Versions\V1\Models\Exceptions\ControllerException;
use App\Versions\V1\Models\Exceptions\ModelException;
use App\Versions\V1\Models\Mongo;
use App\Versions\V1\Utilities\Tweaks;
use App\System\HTTP;
use App\System\Utilities\Config;
use App\System\Utilities\Messages;



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
        $this->messages = Messages::load('v1', 'user');

        $client = new Mongo();
        
        $this->collection = $client->getCollection(
            $this->collectionName
        );

        if ($userId) {

            $user = $this->get($userId);

            if ($user) {
                $this->collectionItem = $user;
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
            throw new ModelException($this->messages['password']['empty'], HTTP::CODE_BAD_REQUEST);
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

            return [
                'id' => $userId,
                'password' => $passwordHashed
            ];

        } catch (\Exception $e) {

            $message = sprintf($this->messages['create']['error'], $e->getMessage());

            throw new DatabaseException($message, HTTP::CODE_SERVER_ERROR);
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
            throw new ModelException($this->messages['id']['empty'], HTTP::CODE_BAD_REQUEST);
        }
        elseif($userIdLength != $userIdLengthDefault) {
            $message = sprintf($this->messages['id']['length'], $userIdLengthDefault);

            throw new ModelException($message, HTTP::CODE_BAD_REQUEST);
        }

        try {
            $result = $this->collection->findOne([
                'id' => $userId   
            ]);

        } catch (\Exception $e) {
            $message = sprintf($this->messages['isset']['notIsset'], $e->getMessage());
            throw new DatabaseException($message, HTTP::CODE_SERVER_ERROR);
        }

        if ($result->ok != 1)
        {

        }

        return $result;
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
        $checkHash = Tweaks::generateHash($password, $user['password']['localSalt']);

        return $user['password']['hash'] === $checkHash['hash'];
    }    
}
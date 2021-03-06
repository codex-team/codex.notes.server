<?php

namespace App\Components\Api\Models;

use App\Components\Base\Models\Mongo;
use App\Components\Notify\Notify;
use App\Components\Sockets\Sockets;
use App\System\Config;
use MongoDB\BSON\ObjectId;

/**
 * Model User
 *
 * @package App\Components\Api\Models
 */
class User extends Base
{
    /**
     * User's unique identifier
     *
     * @var string|null
     */
    public $id;

    /**
     * User's nickname
     *
     * @var string|null
     */
    public $name;

    /**
     * User's email address
     *
     * @var string|null
     */
    public $email;

    /**
     * User's photo URL
     *
     * @var string|null
     */
    public $photo;

    /**
     * User's google id
     *
     * @var string|null
     */
    public $googleId;

    /**
     * Registration timestamp
     *
     * @var int|null
     */
    public $dtReg;

    /**
     * Modified date timestamp
     *
     * @var int|null
     */
    public $dtModify;

    /**
     * User's folders
     *
     * @var array
     */
    public $folders = [];

    /**
     * Collection name
     *
     * @var string|null
     */
    private $collectionName;

    /**
     * User constructor
     *
     * @param string|null $id       — if passed, returns filled User model
     * @param string|null $googleId — try to find user by googleId
     * @param string|null $email    — try to find user by email
     */
    public function __construct($id = '', $googleId = '', $email = '')
    {
        $this->collectionName = self::getCollectionName();

        if ($id || $googleId || $email) {
            $this->findAndFill($id, $googleId, $email);
        }
    }

    /**
     * Create a new User or update existing
     *
     * @param array $data
     */
    public function sync(array $data): void
    {
        $query = [];

        if (isset($data['id'])) {
            $query['_id'] = new ObjectId($data['id']);
        }

        if (isset($data['googleId'])) {
            $query['googleId'] = $data['googleId'];
        }

        $update = [
            '$set' => $data
        ];

        $options = [
            'upsert' => true,
            'returnDocument' => \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER
        ];

        $mongoResponse = Mongo::connect()
            ->{$this->collectionName}
            ->findOneAndUpdate($query, $update, $options);

        /** mongoResponse could be NULL if no item was found */
        $this->fillModel($mongoResponse ?: $data);
    }

    /**
     * Fill User's Folders by models
     *
     * @param int   $limit how much items do you need
     * @param int   $skip  how much items needs to be skipped
     * @param array $sort  sort fields
     */
    public function fillFolders(int $limit = null, int $skip = null, array $sort = []): void
    {
        $foldersCollection = Folder::getCollectionName($this->id);

        $query = [];

        /**
         * Add checking "isRemoved != true" to query
         */
        if (!Config::get('RETURN_REMOVED_ITEMS')) {
            $query['isRemoved'] = [
                '$ne' => true
            ];
        }

        $options = [
            'limit' => $limit,
            'skip' => $skip,
            'sort' => $sort
        ];

        $mongoResponse = Mongo::connect()
            ->{$foldersCollection}
            ->find($query, $options);

        foreach ($mongoResponse as $folder) {
            if (!empty($folder['isShared']) && $folder['isShared']) {
                /** Get real Folder if this element is a link */
                $folderModel = new Folder($folder['ownerId'], $folder['id']);
            } else {
                /** Create Folder model from this data */
                $folderModel = new Folder($this->id, null, $folder);
            }

            $this->folders[] = $folderModel;
        }
    }

    /**
     * Find User by id or googleId and fill put data into model
     *
     * @var string|null $userId
     * @var string|null $googleId
     * @var string      $email
     *
     * @param mixed $userId
     * @param mixed $googleId
     */
    private function findAndFill($userId, $googleId = '', string $email = ''): void
    {
        $query = [];

        if (!empty($googleId)) {
            $query['googleId'] = $googleId;
        }

        if (!empty($email)) {
            $query['email'] = $email;
        }

        if (!empty($userId)) {
            $query['_id'] = new ObjectId($userId);
        }

        $mongoResponse = Mongo::connect()
            ->{$this->collectionName}
            ->findOne($query);

        $this->fillModel($mongoResponse ?: []);
    }

    /**
     * Fill model with values from data
     * Rewrite MongoID _id to string id
     *
     * @param array $data
     */
    protected function fillModel(array $data): void
    {
        $data['id'] = !empty($data['_id']) ? (string) $data['_id'] : '';

        parent::fillModel($data);
    }

    /**
     * Return collection name
     *
     * @return string
     */
    public static function getCollectionName(): string
    {
        return 'users';
    }

    /**
     * Get channel name for pushing updates for this user
     *
     * @return string
     */
    public function getSocketChannelName(): string
    {
        return hash_hmac('md5', $this->id, Config::get('SOCKETS_SALT'));
    }

    /**
     * Send data to user's sockets channel
     *
     * @param string $event
     * @param        $data
     * @param User   $sender
     */
    public function notify(string $event, $data, User $sender): void
    {
        $channel = $this->getSocketChannelName();

        Notify::send($channel, $event, $data, $sender);
    }

    /**
     * Set data to be serialized
     *
     * @return User
     */
    public function jsonSerialize(): User
    {
        return $this;
    }
}

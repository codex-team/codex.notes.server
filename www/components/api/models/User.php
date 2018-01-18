<?php

namespace App\Components\Api\Models;

use App\Components\Base\Models\Mongo;
use MongoDB\BSON\ObjectId;

/**
 * Model User
 *
 * @package App\Components\Api\Models
 */
class User extends Base
{
    /**
     * Users unique identifier
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
     * Registration timestamp
     *
     * @var int|null
     */
    public $dtReg;

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
     * @param string|null $id      if passed, returns filled User model
     */
    public function __construct(string $id = null)
    {
        $this->collectionName = self::getCollectionName();

        if ($id) {
            $this->findAndFill($id);
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

        if (isset($data['google_id'])) {
            $query['google_id'] = $data['google_id'];
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
     * @param int $limit    how much items do you need
     * @param int $skip     how much items needs to be skipped
     * @param array $sort   sort fields
     */
    public function fillFolders(int $limit = null, int $skip = null, array $sort = []): void
    {
        $foldersCollection = Folder::getCollectionName($this->id);

        $query = [
            'isRemoved' => [
                '$ne' => true
            ]
        ];

        $options = [
            'limit' => $limit,
            'skip' => $skip,
            'sort' => $sort
        ];

        $mongoResponse = Mongo::connect()
            ->{$foldersCollection}
            ->find($query, $options);

        foreach ($mongoResponse as $folder) {
            $this->folders[] = new Folder($this->id, null, $folder);
        }
    }

    /**
     * Find User by id and fill put data into model
     *
     * @var string $userId
     */
    private function findAndFill(string $userId): void
    {
        $query = [
            '_id' => new ObjectId($userId)
        ];

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
        $data['id'] = (string) $data['_id'];

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
}
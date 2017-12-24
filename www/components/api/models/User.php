<?php

namespace App\Components\Api\Models;

use App\Components\Base\Models\Mongo;

/**
 * Model User
 *
 * @package App\Components\Api\Models
 */
class User
{
    /**
     * Users unique identifier
     *
     * @var int|null
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

            $this->get($id);
        }

    }

    /**
     * Create or update existing user
     *
     * @param array $data
     */
    public function sync(array $data)
    {
        $query = [
            'id' => $data['id']
        ];

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
     * Get user's folders and put into model
     *
     * @param int $limit    how much items do you need
     * @param int $skip     how much items needs to be skipped
     * @param array $sort   sort fields
     */
    public function getFolders(int $limit = null, int $skip = null, array $sort = [])
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
     * Get user's data by id
     *
     * @var string $userId
     */
    private function get(string $userId)
    {
        $query = [
            'id' => $userId
        ];

        $mongoResponse = Mongo::connect()
            ->{$this->collectionName}
            ->findOne($query);

        $this->fillModel($mongoResponse ?: []);
    }

    /**
     * Fill model with values from data
     *
     * @param array $data
     */
    private function fillModel(array $data)
    {
        foreach ($data as $key => $value) {

            if (property_exists($this, $key)) {

                $this->$key = $value;
            }
        }
    }

    /**
     * Return collection name
     *
     * @return string
     */
    private static function getCollectionName(): string
    {
        return 'users';
    }
}
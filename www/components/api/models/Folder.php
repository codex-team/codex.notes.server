<?php

namespace App\Components\Api\Models;

use App\Components\Base\Models\Mongo;

/**
 * Model Folder
 * Operates with collection folders:<userId>
 *
 * @package App\Components\Api\Models
 */
class Folder
{
    /**
     * Folder's id
     *
     * @var string|null
     */
    public $id;

    /**
     * Folder's title
     *
     * @var string|null
     */
    public $title;

    /**
     * Owner's id
     *
     * @var string|null
     */
    public $ownerId;

    /**
     * Created date timestamp
     *
     * @var int|null
     */
    public $dtCreate;

    /**
     * Modified date timestamp
     *
     * @var int|null
     */
    public $dtModify;

    /**
     * Shared state
     *
     * @var boolean|null
     */
    public $isShared;

    /**
     * Removed state
     *
     * @var boolean|null
     */
    public $isRemoved;

    /**
     * Collection name for this folder
     *
     * @var string|null
     */
    private $collectionName;

    /**
     * Initializing model Folder
     *
     * @param string $ownerId
     * @param string $folderId
     * @param array  $data          init model from data
     */
    public function __construct(string $ownerId, string $folderId = null, array $data = null)
    {
        $this->collectionName = self::getCollectionName($ownerId);

        if ($folderId) {

            $this->get($folderId);
        }

        if ($data) {

            $this->fillModel($data);
        }
    }

    /**
     * Create or update existing folder
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
     * Get folder's data by id
     *
     * @var string $folderId
     */
    private function get(string $folderId)
    {
        $query = [
            'id' => $folderId,
            'isRemoved' => [
                '$ne' => true
            ]
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

        $this->owner = new User($this->ownerId);
    }

    /**
     * Compose collection name by pattern folders:<userId>
     *
     * @param string $ownerId
     * @return string
     */
    public static function getCollectionName(string $ownerId): string
    {
        return sprintf('folders:%s', $ownerId);
    }
}
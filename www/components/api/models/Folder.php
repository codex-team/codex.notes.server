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
    public $owner;

    /**
     * Created date timestamp
     *
     * @var int|null
     */
    public $dt_create;

    /**
     * Modified date timestamp
     *
     * @var int|null
     */
    public $dt_modify;

    /**
     * Shared state
     *
     * @var boolean|null
     */
    public $is_shared;

    /**
     * Removed state
     *
     * @var boolean|null
     */
    public $is_removed;

    /**
     * Collection name for this folder
     *
     * @var string|null
     */
    private $collectionName;

    /**
     * Initializing model Folder
     *
     * @param string $userId
     */
    public function __construct(string $userId)
    {
        $this->collectionName = self::collection($userId);
    }

    /**
     * Fill model with values from data
     *
     * @param array $data
     */
    public function fillModel(array $data)
    {
        foreach ($data as $key => $value) {

            if (property_exists($this, $key)) {

                $this->$key = $value;
            }
        }

        $this->owner = new User($this->owner);
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

        $mongoResponse = Mongo::connect()
            ->{$this->collectionName}
            ->findOneAndUpdate($query, $update, ['upsert' => true]);

        /** mongoResponse could be NULL if no item was found */
        $this->fillModel($mongoResponse ?: $data);
    }

    /**
     * Compose collection name by pattern folders:<userId>
     *
     * @param string $userId
     * @return string
     */
    private static function collection(string $userId): string
    {
        return sprintf('folders:%s', $userId);
    }
}
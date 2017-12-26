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
     * List of models of Notes
     *
     * @var array
     */
    public $notes = [];

    /**
     * Folder owner's model
     *
     * @var object|null
     */
    public $owner;

    /**
     * Owner's id
     *
     * @var string|null
     */
    private $ownerId;

    /**
     * Collection name for this Folder
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
        $this->ownerId = $ownerId;
        $this->collectionName = self::getCollectionName($this->ownerId);

        if ($folderId) {
            $this->get($folderId);
        }

        if ($data) {
            $this->fillModel($data);
        }
    }

    /**
     * Create or update an existed Folder
     *
     * @param array $data
     */
    public function sync(array $data): void
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
     * Get notes in this Folder
     *
     * @param int $limit    how much items do you need
     * @param int $skip     how much items needs to be skipped
     * @param array $sort   sort fields
     */
    public function fillNotes(int $limit = null, int $skip = null, array $sort = []): void
    {
        $notesCollection = Note::getCollectionName($this->ownerId, $this->id);

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
            ->{$notesCollection}
            ->find($query, $options);

        foreach ($mongoResponse as $note) {
            $this->notes[] = new Note($this->ownerId, $this->id, null, $note);
        }
    }

    /**
     * Get owner model
     */
    public function fillOwner(): void
    {
        $this->owner = new User($this->ownerId);
    }

    /**
     * Get Folder's data by id
     *
     * @var string $folderId
     */
    private function get(string $folderId): void
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
    private function fillModel(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
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
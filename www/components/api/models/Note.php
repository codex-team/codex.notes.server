<?php

namespace App\Components\Api\Models;

use App\Components\Base\Models\Mongo;

/**
 * Model Note
 * Operates with collection notes:<userId>:<folderId>
 *
 * @package App\Components\Api\Models
 */
class Note
{
    /**
     * Note's id
     *
     * @var string|null
     */
    public $id;

    /**
     * Note's title
     *
     * @var string|null
     */
    public $title;

    /**
     * Note's content
     *
     * @var string|null
     */
    public $content;

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
     * Removed state
     *
     * @var boolean|null
     */
    public $isRemoved;

    /**
     * Note's author
     *
     * @var object|null
     */
    public $author;

    /**
     * Note's owner
     *
     * @var string|null
     */
    private $authorId;

    /**
     * Note's folder
     *
     * @var string|null
     */
    private $folderId;

    /**
     * Collection name
     *
     * @var string
     */
    private $collectionName;

    /**
     * User constructor
     *
     * @param string $authorId
     * @param string $folderId
     * @param string|null $id      if passed, returns filled User model
     * @param array $data
     */
    public function __construct(string $authorId, string $folderId, string $id = null, array $data = null)
    {
        $this->authorId = $authorId;
        $this->folderId = $folderId;
        $this->collectionName = self::getCollectionName($authorId, $folderId);

        if ($id) {
            $this->get($id);
        }

        if ($data) {
            $this->fillModel($data);
        }
    }

    /**
     * Create or update existing note
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
     * Get author model
     */
    public function getAuthor(): void
    {
        $this->author = new User($this->authorId);
    }

    /**
     * Get note's data by id
     *
     * @var string $noteId
     */
    private function get(string $noteId): void
    {
        $query = [
            'id' => $noteId,
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
     * Compose collection name by pattern notes:<userId>:<folderId>
     *
     * @param string $userId
     * @param string $folderId
     * @return string
     */
    public static function getCollectionName(string $userId, string $folderId): string
    {
        return sprintf('notes:%s:%s', $userId, $folderId);
    }
}

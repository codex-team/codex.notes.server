<?php

namespace App\Components\Api\Models;

use App\Components\Base\Models\Mongo;

/**
 * Model Note
 * Operates with collection notes:<userId>:<folderId>
 *
 * @package App\Components\Api\Models
 */
class Note extends Base
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
     * Note author's id
     *
     * @var string|null
     */
    private $authorId;

    /**
     * Note Folder's id
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
     * Note constructor
     *
     * @param string $authorId
     * @param string $folderId
     * @param string|null $id      if passed, returns filled Note model
     * @param array $data
     */
    public function __construct(string $authorId, string $folderId, string $id = null, array $data = null)
    {
        $this->authorId = $authorId;
        $this->folderId = $folderId;
        $this->collectionName = self::getCollectionName($authorId, $folderId);

        if ($id) {
            $this->findAndFill($id);
        }

        if ($data) {
            $this->fillModel($data);
        }
    }

    /**
     * Create or update an existed Note
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
     * Fill User author's model
     */
    public function fillAuthor(): void
    {
        $this->author = new User($this->authorId);
    }

    /**
     * Find Note by id and fill put data into model
     *
     * @var string $noteId
     */
    private function findAndFill(string $noteId): void
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

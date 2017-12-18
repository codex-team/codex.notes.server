<?php

namespace App\Components\Api\Models;

/**
 * Model Notes
 * Operates with collection notes:<userId>:<folderId>
 * @package App\Components\Api\Models
 */
class Notes
{
    /**
     * @var object MongoDB\Collection
     */
    private $collection;

    /**
     * @var array|null  List of notes in the folder
     */
    public $items;

    /**
     * User constructor.
     * @param int $userId      Owner user id
     * @param int $folderId    Folder id
     */
    public function __construct(int $userId, int $folderId)
    {
        /**
         * @todo Construct collection and return items
         */
        $this->collection = self::collection($userId, $folderId);
        $this->items = [['note1'], ['note2'], ['note3']];
    }

    /**
     * Compose collection name by pattern notes:<userId>:<folderId>
     * @param int $userId
     * @param int $folderId
     * @return string
     */
    private static function collection(int $userId, int $folderId): string
    {
        return sprintf('notes:%u:%u', $userId, $folderId);
    }
}
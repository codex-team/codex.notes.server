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
     * List of notes in the folder
     *
     * @var array|null
     */
    public $items;

    /**
     * Collection name
     *
     * @var string
     */
    private $collection;

    /**
     * User constructor
     *
     * @param string $userId      Owner user id
     * @param string $folderId    Folder id
     */
    public function __construct(string $userId, string $folderId)
    {
        /**
         * @todo Construct collection and return items
         */
        $this->collection = self::collection($userId, $folderId);
        $this->items = ['note1', 'note2'];
    }

    /**
     * Compose collection name by pattern notes:<userId>:<folderId>
     *
     * @param string $userId
     * @param string $folderId
     * @return string
     */
    private static function collection(string $userId, string $folderId): string
    {
        return sprintf('notes:%s:%s', $userId, $folderId);
    }
}

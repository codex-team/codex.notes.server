<?php

namespace App\Components\Api\Models;

/**
 * Model Folders
 * Operates with collection folders:<userId>
 * @package App\Components\Api\Models
 */
class Folders
{
    /**
     * @var object MongoDB\Collection
     */
    private $collection;

    /**
     * @var array|null  List of folders for passed user
     */
    public $items;

    /**
     * User constructor.
     * @param int $userId      Owner user id
     */
    public function __construct(int $userId)
    {
        /**
         * @todo Construct collection and return items
         */
        $this->collection = self::collection($userId);
        $this->items = [['folder1'], ['folder2'], ['folder3']];
    }

    /**
     * Compose collection name by pattern notes:<userId>:<folderId>
     * @param int $userId
     * @return string
     */
    private static function collection(int $userId): string
    {
        return sprintf('folders:%u', $userId);
    }
}
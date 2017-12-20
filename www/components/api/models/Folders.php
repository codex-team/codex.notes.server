<?php

namespace App\Components\Api\Models;

use App\Components\Sglobal\Models\Mongo;

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
        $this->collectionName = self::collection($userId);

        $this->mongo = new Mongo();

        // $this->items = [['folder1'], ['folder2']];
    }

    /**
     *
     */
    public function insert($data)
    {
        $this->mongo->insert($this->collectionName, $data);
    }

    /**
     * Compose collection name by pattern folders:<userId>
     * @param int $userId
     * @return string
     */
    private static function collection(int $userId): string
    {
        return sprintf('folders:%u', $userId);
    }
}

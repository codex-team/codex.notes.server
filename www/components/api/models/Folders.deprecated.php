<?php

namespace App\Components\Api\Models;

use App\Components\Base\Models\Mongo;

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
    public function __construct(string $userId)
    {
        $this->collectionName = self::collection($userId);

        $this->mongo = new Mongo();
    }

    /**
     * Create a new folder
     * @param array $data
     * @return object
     * @throws
     */
    public function create($data): Folder
    {
        $folder = new Folder($data);

        $mongoResponse = $this->mongo->insert($this->collectionName, $data);

        $folder->id = (string) $mongoResponse->getInsertedId();

        return $folder;
    }

    /**
     * Delete folder by id
     * @param array $data
     * @return object
     * @throws
     */
    public function delete($data): bool
    {
        $mongoResponse = $this->mongo->remove($this->collectionName, $data);

        return (boolean) $mongoResponse->ok;
    }



    /**
     * Rename folder
     */
    public function rename($data): bool
    {
        $folder = new Folder($data);

        $filter = ['_id' => new MongoId($data['id'])];

        $mongoResponse = $this->mongo->update($this->collectionName, $filter, $data);

        return (boolean) $mongoResponse->ok;
    }

    /**
     * Compose collection name by pattern folders:<userId>
     * @param int $userId
     * @return string
     */
    private static function collection(string $userId): string
    {
        return sprintf('folders:%u', $userId);
    }
}

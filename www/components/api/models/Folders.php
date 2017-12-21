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
    public function __construct(int $userId)
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
    public function create($data)
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
    public function delete($data)
    {
        $mongoResponse = $this->mongo->remove($this->collectionName, $data);

        return (boolean) $mongoResponse->ok;
    }



//    /**
//     * Update the existing folder
//     */
//    public function update($data)
//    {
//        $this->mongo->update($this->collectionName, $data);
//    }

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

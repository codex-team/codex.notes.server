<?php

namespace App\Components\Api\Models;

use App\Components\Base\Models\Mongo;

/**
 * Collaborator Model
 *
 * @package App\Components\Api\Models
 */
class Collaborator
{
    /**
     * Collaboratior User's id
     *
     * @var string|null
     */
    public $id;

    /**
     * User's email address
     *
     * @var string|null
     */
    public $email;

    /**
     * Invite Collaborator timestamp
     *
     * @var int|null
     */
    public $dtInvite;

    /**
     * Collaboratior's User model
     */
    public $user;

    /**
     * Removed state
     *
     * @var boolean|null
     */
    public $isRemoved;

    /**
     * Folder model
     *
     * @var string|null
     */
    private $folder;

    /**
     * Collection name
     *
     * @var string
     */
    private $collectionName;

    /**
     * Initializing model Collaborator
     *
     * @param object $folder
     * @param string $id
     * @param array  $data          init model from data
     */
    public function __construct(Folder $folder = null, string $id = null, array $data = null)
    {
        $this->folder = $folder;
        $this->collectionName = self::getCollectionName($this->folder->ownerId, $this->folder->id);

        if ($id) {
            $this->findAndFill($id);
        }

        if ($data) {
            $this->fillModel($data);
        }
    }

    /**
     * Create or update an existed Collaborator
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
     * Fill User's model
     */
    public function fillUser(): void
    {
        $this->user = new User($this->id);
    }

    /**
     * Find Collaborator by id and put data into model
     *
     * @var string $id
     */
    private function findAndFill(string $id): void
    {
        $query = [
            'id' => $id,
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
     * Compose collection name by pattern collaborators:<owner_id>:<folder_id>
     *
     * @param string $ownerId
     * @return string
     */
    public static function getCollectionName(string $ownerId, string $folderId): string
    {
        return sprintf('collaborators:%s:%s', $ownerId, $folderId);
    }
}
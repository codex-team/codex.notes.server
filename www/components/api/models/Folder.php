<?php

namespace App\Components\Api\Models;

use App\Components\Base\Models\Exceptions\FolderException;
use App\Components\Base\Models\Mongo;

/**
 * Model Folder
 * Operates with collection folders:<userId>
 *
 * @package App\Components\Api\Models
 */
class Folder extends Base
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
     * Folder's shared status:
     *  1) false — this is original Folder
     *  2) true  — this is 'virtual' shared Folder
     *
     * @var bool
     */
    public $isShared = false;

    /**
     * Removed state
     *
     * @var bool
     */
    public $isRemoved = false;

    /**
     * Is this Root Folder
     *
     * @var bool
     */
    public $isRoot = false;

    /**
     * List of models of Notes
     *
     * @var array
     */
    public $notes = [];

    /**
     * List of Collaborators (User model)
     *
     * @var array
     */
    public $collaborators = [];

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
    public $ownerId;

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
     * @param string $id
     * @param array  $data    init model from data
     */
    public function __construct(string $ownerId, string $id = null, array $data = null)
    {
        $this->ownerId = $ownerId;
        $this->collectionName = self::getCollectionName($this->ownerId);

        if ($id) {
            $this->findAndFill($id);
        }

        if ($data) {
            $this->fillModel($data);
        }
    }

    /**
     * Override model fill method
     *
     * @param array $data
     */
    protected function fillModel(array $data): void
    {
        parent::fillModel($data);

        /**
         * And fill Notes list
         */
        $this->fillNotes();
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
     * Fill Notes in this Folder
     *
     * @param int   $limit how much items do you need
     * @param int   $skip  how much items needs to be skipped
     * @param array $sort  sort fields
     */
    public function fillNotes(int $limit = null, int $skip = null, array $sort = []): void
    {
        /**
         * Where Notes stored
         */
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
     * Fill Collaborators in this Folder
     *
     * @param int   $limit how much items do you need
     * @param int   $skip  how much items needs to be skipped
     * @param array $sort  sort fields
     */
    public function fillCollaborators(int $limit = null, int $skip = null, array $sort = []): void
    {
        if (!$this->ownerId || !$this->id) {
            throw new FolderException('Folder does not exist');
        }

        $collaboratorsCollection = Collaborator::getCollectionName($this->ownerId, $this->id);

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
            ->{$collaboratorsCollection}
            ->find($query, $options);

        foreach ($mongoResponse as $collaboratorRow) {
            $collaborator = new Collaborator($this, null, $collaboratorRow);

            $collaborator->fillUser();

            $this->collaborators[] = $collaborator;
        }
    }

    /**
     * Fill Folder's owner User model
     */
    public function fillOwner(): void
    {
        $this->owner = new User($this->ownerId);
    }

    /**
     * Find Folder by id and put data into model
     *
     * @var string $folderId
     */
    private function findAndFill(string $folderId): void
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
     * Compose collection name by pattern folders:<userId>
     *
     * @param string $ownerId
     *
     * @return string
     */
    public static function getCollectionName(string $ownerId): string
    {
        return sprintf('folders:%s', $ownerId);
    }
}

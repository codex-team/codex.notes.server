<?php

namespace App\Components\Api\Models;

use App\Components\Base\Models\Exceptions\CollaboratorException;
use App\Components\Base\Models\Mailer;
use App\Components\Base\Models\Mongo;
use App\System\Config;
use App\System\Renderer;

/**
 * Collaborator Model
 *
 * @package App\Components\Api\Models
 */
class Collaborator extends Base
{
    /**
     * Collaborator's Invitation Token
     *
     * @var string|null
     */
    public $token;

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
     * Collaboratior User's id
     * Null until user was not accepted the invitation
     *
     * @var string|null
     */
    public $userId;

    /**
     * Collaboratior's User model
     *
     * @var string|null
     */
    public $user;

    /**
     * Removed state
     *
     * @var bool|null
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
     * @param Folder $folder
     * @param string $token
     * @param array  $data   init model from data
     *
     * @throws CollaboratorException
     */
    public function __construct(Folder $folder = null, string $token = null, array $data = null)
    {
        $this->folder = $folder;

        if (!$this->folder->ownerId || !$this->folder->id) {
            throw new CollaboratorException('Folder does not exist');
        }

        $this->collectionName = self::getCollectionName($this->folder->ownerId, $this->folder->id);

        if ($token) {
            $this->findAndFill($token);
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
            'token' => $data['token']
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
        $this->user = new User($this->userId);
    }

    /**
     * Find Collaborator by id and put data into model
     *
     * @var string $id
     */
    private function findAndFill(string $token): void
    {
        $query = [
            'token' => $token,
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
     * On the Invitation acceptance
     * Save Shared Folder to the Collaborator's Folders collection
     *
     * @param Folder $folder - Folder that was shared
     */
    public function saveFolder(Folder $folder): void
    {
        $acceptorsFolder = new Folder($this->userId);

        $acceptorsFolder->sync([
            'id' => $folder->id,
            'ownerId' => $folder->ownerId,
            'isShared' => true,
            'title' => $folder->title,
            'dtCreate' => $folder->dtCreate,
            'dtModify' => $folder->dtModify,
            'isRemoved' => $folder->isRemoved,
        ]);
    }

    /**
     * Compose collection name by pattern collaborators:<owner_id>:<folder_id>
     *
     * @param string $ownerId
     * @param string $folderId
     *
     * @return string
     */
    public static function getCollectionName(string $ownerId, string $folderId): string
    {
        return sprintf('collaborators:%s:%s', $ownerId, $folderId);
    }

    /**
     * Generate an Invitation token
     *
     * @param string $userId
     * @param string $folderId
     * @param string $email
     *
     * @return string
     */
    public static function getInvitationToken(string $userId, string $folderId, string $email): string
    {
        $secretString = sprintf('%s:%s:%s', $userId, $folderId, $email);

        return hash_hmac('sha256', $secretString, Config::get('INVITATION_SALT'));
    }

    /**
     * Send invitation email to the collaborator
     *
     */
    public function sendInvitationEmail(): bool
    {
        $invitedUser = new User($this->userId);
        $folderOwner = new User($this->folder->ownerId);

        if (!$invitedUser || !$folderOwner) {
            return false;
        }

        $message = Renderer::render(
            'email.php', [
                'invited_username' => $invitedUser->name,
                'owner_username' => $folderOwner->name,
                'folder_title' => $this->folder->title,
                'join_link' => Config::get('SERVER_URI') . 'join/' . urlencode($invitedUser->email) . '/' . $this->token
            ], null
        );

        $mailer = Mailer::instance();

        return $mailer->send("[CodeX Notes] Join folder – " . $this->folder->title, $this->email, $invitedUser->email, $message);
    }
}

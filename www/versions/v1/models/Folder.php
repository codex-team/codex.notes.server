<?php

namespace App\Versions\V1\Models;

use App\System\Utilities\Messages;
use APP\System\HTTP;
use App\Versions\V1\Models\Mongo;
use App\Versions\V1\Models\Exceptions\ControllerException;

/**
 * Class Folder
 * Модель для работы с коллекцией директории в MongoDB
 *
 * @package App\Versions\V1\Models
 */
class Folder extends Base
{
    private $client;

    /**
     * Имя коллекции в Mongo
     * @var string
     */
    private $collectionName = 'folder';
    
    public function __construct()
    {
        parent::__construct();

        $this->messages = Messages::load('v1', 'folder');

        $this->client = new Mongo();
    }

    /**
     * Создаем коллекцию папок для пользователя и в нее вставляем новую папку
     *
     * @param string $user      User id
     * @param string $name      Folder name
     * @param string $id        Folder id
     * @param int    $timestamp unix time
     *
     * @return bool | exception
     * @throws ControllerException
     */
    public function create(string $user = '', string $name = '', string $id = '', int $timestamp = 0 )
    {
        if (!$user) {
            throw new ControllerException($this->messages['user']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        if (!$name) {
            throw new ControllerException($this->messages['folder']['name']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        if (!$id) {
            throw new ControllerException($this->messages['folder']['id']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        $folderIdUidCollectionName = $this->collectionName . ':' . $id . ':' . $user;

        $folderIdUid = $this->client->createCollection($folderIdUidCollectionName);

        $folders = new Folders();
        $folderUid = $folders->create($user, $name, $id, $timestamp);

        return true;
    }

    /**
     * Удаляем папку из коллекции папок пользователя
     *
     * @param string $user  User id
     * @param string $id    Folder id
     *
     * @return bool | exception
     * @throws ControllerException
     */
    public function delete(string $user = '', string $id = '')
    {
        if (!$user) {
            throw new ControllerException($this->messages['user']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        if (!$id) {
            throw new ControllerException($this->messages['folder']['id']['empty'], HTTP::CODE_BAD_REQUEST);
        }

        $folderCollection = $this->collectionName .= ':' . $id . ':' . $user;

        $this->client->deleteCollection($folderCollection);

        $folders = new Folders();
        $foldersCollection = $folders->delete($user, $id);

        return true;
    }

    /**
     * Add collaborator to folder
     *
     * @param string $userId
     * @param string $folderId
     * @param string $email
     * @return bool
     */
    public function addCollaborator(string $userId, string $folderId, string $email)
    {

        $collaboratorsCollection = 'collaborators:' . $userId . ':' . $folderId;

        $this->client->insert($collaboratorsCollection, [
            'email' => $email,
            'collaborator_id' => null,
            'invitation_token' => $this->getInvitationToken($userId, $folderId, $email),
            'dt_add' => time(),
            'accepted' => false
        ]);

        return true;

    }

    public function verifyCollaborator(string $userId, string $email, string $token)
    {

        list($ownerId, $folderId, $signature) = explode(':', $token);

        $verificationToken = $this->getInvitationToken($ownerId, $folderId, $email);

        if ($verificationToken !== $token) {
            return false;
        }

        $collaboratorsCollection = 'collaborators:' . $ownerId . ':' . $folderId;

        $this->client->update($collaboratorsCollection,
            ['invitation_token' => $token],
            ['$set' =>
                [
                    'accepted' => true,
                    'collaborator_id' => $userId
                ]
            ]
        );

        $this->shareFolder($ownerId, $folderId, $userId);

        $folder = $this->client->find('directory:' . $ownerId . ':' . $folderId, []);
        return $folder;

    }

    public function shareFolder($ownerId, $folderId, $userId)
    {

        /** @TODO: check if folder or users don't exist */

        $ownerCollection = 'directories:' . $ownerId;
        $invitedCollection = 'directories:' . $userId;

        $this->client->update($ownerCollection, [
            '_id' => $folderId
        ],
        [
            '$set' => [
                'is_shared' => true,
                'sharer_id' => $ownerId
            ]
        ]);

        $this->client->update($invitedCollection,
        [
            '_id' => $folderId
        ],
        [
            '$set' => [
                '_id' => $folderId,
                'is_shared' => true,
                'sharer_id' => $ownerId,
                'title' => 'Default title'
            ]
        ],
        [
            'upsert' => true
        ]);

        return true;

    }

    private function getInvitationToken(string $userId, string $folderId, string $email)
    {

        return $userId . ':' . $folderId . ':' . hash_hmac('sha256', $userId . $folderId . $email, $_SERVER['INVITATION_SALT']);

    }

}
<?php

namespace App\tests\helpers;

use App\Components\Base\Models\Mongo;
use App\Components\OAuth\OAuth;
use MongoDB\BSON\ObjectId;

class Data
{
    /**
     * @var string
     */
    private $jwt;

    /**
     * @var string
     */
    private $channel;

    /**
     * @var array
     */
    private $user;
    private $folder;
//    public $note;
//    public $collaborator;

    public function __construct($dropDatabase = false)
    {
        if ($dropDatabase) {
            $this->dropDatabase();
        }

        $this->resetUserData();
    }

    /**
     * Get JWT for this User
     *
     * @throws \Exception
     *
     * @return string
     *
     */
    public function getJWT(): string
    {
        if (empty($this->jwt)) {
            $this->resetUserData();
        }

        return $this->jwt;
    }

    /**
     * Get User
     *
     * @return array
     */
    public function getUserData(): array
    {
        if (empty($this->user)) {
            $this->resetUserData();
        }

        return $this->user;
    }

    /**
     * Get Folder
     *
     * @return array
     */
    public function getFolderData(): array
    {
        if (empty($this->folder)) {
            $this->prepareFolder();
        }

        return $this->folder;
    }

    /**
     * Get Note
     *
     * @return array
     */
    public function getNoteData(): array
    {
        if (empty($this->note)) {
            $this->prepareNote();
        }

        return $this->note;
    }

    public function updateData($data): void
    {
        foreach ($data as $field => $value) {
            $this->user[$field] = $value;
        }
    }

    private function dropDatabase(): void
    {
        Mongo::connect()->drop();
    }

    private function resetUserData()
    {
        $userIdentifier = rand(1, 1000000);

        /**
         * Set Default params for user
         */
        $userData = [
            'name' => 'John Doe #' . $userIdentifier,
            'email' => 'user' . $userIdentifier . '@ifmo.su',
            'photo' => 'https://capella.pics/bb523ae5-9c44-4838-bb59-456d7e2a09f3',
            'googleId' => $userIdentifier
        ];

        /**
         * OAuth::generateJwtWithUserData requires googleId in id field
         * And picture instead of photo
         * This way Google responses on User's auth
         */
        $userData['id'] = $userData['googleId'];
        $userData['picture'] = $userData['photo'];

        /**
         * Auth user and save data
         * - user data with id
         * - jwt
         * - channel
         */
        $jwtWithUserData = $this->authUser((object) $userData);

        /**
         * Write real User's id
         */
        $userData['id'] = $jwtWithUserData['id'];
        unset($userData['picture']);
        $this->jwt = $jwtWithUserData['jwt'];
        $this->channel = $jwtWithUserData['channel'];

        $this->user = $userData;
    }

    private function prepareFolder()
    {
        $this->folder = [
            'id' => (string) new ObjectId(),
            'ownerId' => $this->user['id'],
            'title' => 'new folder',
            'dtCreate' => 1517651704,
            'dtModify' => 1517651704,
            'isShared' => false,
            'isRemoved' => false
        ];
    }

    private function prepareNote()
    {
        $this->note = [
            'id' => (string) new ObjectId(),
            'authorId' => $this->user['id'],
            'folderId' => $this->folder['id'],
            'title' => 'my secret note',
            'content' => '[{"type":"paragraph","data":{"text":"<p>Hello world!</p>","format":"html","introText":"<<same>>"}}]',
            'dtCreate' => 1517651704,
            'dtModify' => 1517651704,
            'isRemoved' => false
        ];
    }

    private function authUser($userData): array
    {
        $jwtWithUserData = OAuth::generateJwtWithUserData($userData);

        return $jwtWithUserData;
    }
}

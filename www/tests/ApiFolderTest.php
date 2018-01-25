<?php

namespace App\Tests;

use App\Components\Api\Models\Folder;
use App\Components\Base\Models\Mongo;
use App\Tests\Helpers\WebTestCase;
use App\Tests\Models\CollaboratorsModel;
use App\Tests\Models\FoldersModel;
use App\Tests\Models\UsersModel;
use MongoDB\BSON\ObjectId;

/**
 * Class ApiUserTest
 *
 * @package App\Tests
 *
 * Test GraphQl folder API endpoint
 */
class ApiFolderTest extends WebTestCase
{
    /**
     * Setup testing environment
     */
    public function setup()
    {
        parent::setup();
        $this->dropCollection();
    }

    /**
     * Drop folders collection from test database
     */
    private function dropCollection()
    {
        $db = Mongo::connect();
        foreach ($db->listCollections() as $collectionInfo) {
            $db->selectCollection($collectionInfo->getName())->drop();
        }
    }

    /**
     * Test Folder Model – Create new folder
     *
     * Create new folder with GraphQl request and find it with model
     */
    public function testCreateNewFolderOnlyModel()
    {
        $userId = (string) new ObjectId();
        $folderId = (string) new ObjectId();

        // Create new user
        $newUser = new UsersModel($userId, 'testFindUser', 'testFindUser@ifmo.su', 123);

        // Create folder for the user
        $newFolder = new FoldersModel($userId, $folderId, 'new folder', []);

        // Find the folder via model
        $checkFolder = new Folder($userId, $folderId);

        $this->assertEquals($checkFolder->id, $folderId);
    }

    /**
     * Test API Mutation – Create new folder
     *
     * Create new folder with GraphQl request and find it with model
     */
    public function testCreateNewFolder()
    {


    }

//    public function testFolderCreateGrap
//
    public function testQueryFolder()
    {
        $userId = (string) new ObjectId();
        $folderId = (string) new ObjectId();
        $userIdToInvite = (string) new ObjectId();

        $newUser = new UsersModel($userId, 'testFindUser', 'testFindUser@ifmo.su', 123);
        $userToInvite = new UsersModel($userIdToInvite, 'userToInvite', '3285b08cb2-87bb61@inbox.mailtrap.io', 123);

        $newFolder = new FoldersModel($userId, $folderId, 'new folder', []);

        $folderQuery = FoldersModel::getFindFolderQuery((string) $folderId, (string) $userId);
        $output = $this->client->post('/graphql', $folderQuery);
        $data = json_decode($output, true);

        $collaboratorMutation = CollaboratorsModel::getCreateNewCollaboratorMutation((string) $userIdToInvite, (string) $userId, (string) $folderId, 'userToInvite@ifmo.su', 123, false);

        $folderMutation = FoldersModel::getCreateNewUserMutation((string) $folderId, (string) $userId, 'new folder', 123, 123, false, false);
        $output = $this->client->post('/graphql', $collaboratorMutation);
        $data = json_decode($output, true);
    }
}

<?php

namespace App\Tests;

use App\Components\Api\Models\Folder;
use App\Components\Base\Models\Mongo;
use App\Tests\Helpers\WebTestCase;
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
     * Compare model and array structure
     *
     * @param $folderModel
     * @param $folder
     */
    private function compare($folderModel, $folder)
    {
        // check if initial and saved models are equal
        $this->assertEquals($folderModel->id, $folder['id']);
        $this->assertEquals($folderModel->title, $folder['title']);
        $this->assertEquals($folderModel->ownerId, $folder['owner']['id']);
        $this->assertEquals($folderModel->dtCreate, $folder['dtCreate']);
        $this->assertEquals($folderModel->dtModify, $folder['dtModify']);
        $this->assertEquals($folderModel->isShared, $folder['isShared']);
        $this->assertEquals($folderModel->isRemoved, $folder['isRemoved']);
    }

    /**
     * Test Folder Model – Create new folder
     *
     * Create new folder with model and find it with model
     */
    public function testCreateNewFolderOnlyModel()
    {
        $userId = (string) new ObjectId();
        $folderId = (string) new ObjectId();

        // Create new folder
        $newUser = new UsersModel($userId, 'testFindUser', 'testFindUser@ifmo.su', 123);

        // Create folder for the user
        $newFolder = new FoldersModel($userId, $folderId, ['title' => 'new folder']);

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
        $userId = (string) new ObjectId();
        $folderId = (string) new ObjectId();

        // Create new folder
        $newUser = new UsersModel($userId, 'testFindUser', 'testFindUser@ifmo.su', 123);
        $folderMutation = FoldersModel::getCreateNewFolderMutation((string) $folderId, (string) $userId, 'new folder', 123, 123, false, false);
        $output = $this->client->post('/graphql', $folderMutation);
        $data = json_decode($output, true);

        // check json output structure
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('folder', $data['data']);
        $this->assertArrayHasKey('owner', $data['data']['folder']);

        $folder = $data['data']['folder'];

        // get Folder from DB by model
        $folderModel = new Folder($userId, $folderId);

        // check if initial and saved models are equal
        $this->compare($folderModel, $folder);
    }

    /**
     * Test API Mutation – Create new folder and find it
     *
     * Create new folder with model and find it with GraphQl request
     */
    public function testFindFolder()
    {
        $userId = (string) new ObjectId();
        $folderId = (string) new ObjectId();

        // Create new folder
        $newUser = new UsersModel($userId, 'testFindUser', 'testFindUser@ifmo.su', 123);
        $folderModel = new FoldersModel($userId, $folderId, ['title' => 'new folder']);

        $folderQuery = FoldersModel::getFindFolderQuery($folderModel->id, $userId);
        $output = $this->client->post('/graphql', $folderQuery);
        $data = json_decode($output, true);

        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('folder', $data['data']);
        $this->assertArrayHasKey('owner', $data['data']['folder']);

        $folder = $data['data']['folder'];

        // check if initial and found models are equal
        $this->compare($folderModel, $folder);
    }

    /**
     * Test API Mutation – Create new folder and find it
     *
     * Create new folder find it with GraphQl requests
     */
    public function testCreateNewFolderAndFind()
    {
        $userId = (string) new ObjectId();
        $folderId = (string) new ObjectId();

        // Create new folder
        $newUser = new UsersModel($userId, 'testFindUser', 'testFindUser@ifmo.su', 123);
        $folderMutation = FoldersModel::getCreateNewFolderMutation((string) $folderId, (string) $userId, 'new folder', 123, 123, false, false);
        $output = $this->client->post('/graphql', $folderMutation);
        $data = json_decode($output, true);

        // check json output structure
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('folder', $data['data']);
        $this->assertArrayHasKey('owner', $data['data']['folder']);

        $createdFolder = $data['data']['folder'];

        $folderQuery = FoldersModel::getFindFolderQuery($folderId, $userId);
        $output = $this->client->post('/graphql', $folderQuery);
        $data = json_decode($output, true);

        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('folder', $data['data']);
        $this->assertArrayHasKey('owner', $data['data']['folder']);

        $foundFolder = $data['data']['folder'];

        // check if initial and found models are equal
        $this->assertEquals($createdFolder['id'], $foundFolder['id']);
        $this->assertEquals($createdFolder['title'], $foundFolder['title']);
        $this->assertEquals($createdFolder['ownder']['id'], $foundFolder['ownder']['id']);
        $this->assertEquals($createdFolder['dtCreate'], $foundFolder['dtCreate']);
        $this->assertEquals($createdFolder['dtModify'], $foundFolder['dtModify']);
        $this->assertEquals($createdFolder['isShared'], $foundFolder['isShared']);
        $this->assertEquals($createdFolder['isRemoved'], $foundFolder['isRemoved']);
    }
}

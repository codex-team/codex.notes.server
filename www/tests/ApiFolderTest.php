<?php

namespace App\Tests;

use App\Components\Api\Models\Folder;
use App\Components\Api\Models\User;
use App\Components\Base\Models\Mongo;
use App\Tests\Helpers\GraphQl;
use App\Tests\Helpers\WebTestCase;
use MongoDB\BSON\ObjectId;

/**
 * Class ApiFolderTest
 *
 * @package App\Tests
 *
 * Test GraphQl folder API endpoint
 */
class ApiFolderTest extends WebTestCase
{
    private $testUser;
    private $testFolder;

    /**
     * Setup testing environment
     */
    public function setup()
    {
        parent::setup();
        $this->dropCollection();
        $this->initDb();
    }

    /**
     * Initialize database with test user and folder
     */
    private function initDb()
    {
        $this->testUser = new User((string) new ObjectId());
        $this->testUser->sync([
            'name' => 'JohnDoe',
            'email' => 'JohnDoe@ifmo.su',
            'dtReg' => 1517651704
        ]);

        $id = (string) new ObjectId();
        $this->testFolder = new Folder($this->testUser->id, $id);
        $this->testFolder->sync([
            'id' => $id,
            'title' => 'new folder',
            'dtCreate' => 1517651704,
            'dtModify' => 1517651704,
            'isShared' => false,
            'isRemoved' => false
        ]);
    }

    /**
     * Drop collections from test database
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
     * Test Folder Model – Get test folder
     *
     * Find test folder with model
     */
    public function testGetFolder()
    {
        $folder = new Folder($this->testUser->id, $this->testFolder->id);

        $this->assertEquals($this->testFolder->id, $folder->id);
    }

    /**
     * Test API Mutation – Create new folder
     *
     * Create folder with GraphQl request and find it with model
     */
    public function testCreateFolder()
    {
        $folderId = (string) new ObjectId();

        $data = $this->sendGraphql(GraphQl::MUTATION, 'CreateNewFolder', [
            'id' => $folderId,
            'ownerId' => $this->testUser->id,
            'title' => 'new folder',
            'dtCreate' => 1517651704,
            'dtModify' => 1517651704,
            'isShared' => false,
            'isRemoved' => false
        ]);

        $this->assertArrayHasKey('folder', $data);
        $this->assertArrayHasKey('owner', $data['folder']);

        $folder = $data['folder'];

        // get Folder from DB by model
        $folderModel = new Folder($this->testUser->id, $folderId);

        // check if initial and saved models are equal
        $this->compare($folderModel, $folder);
    }

    /**
     * Test API Mutation – Find folder
     *
     * Find existing folder with GraphQl request
     */
    public function testFindFolder()
    {
        $data = $this->sendGraphql(GraphQl::QUERY, 'GetFolder', [
            'id' => $this->testFolder->id,
            'ownerId' => $this->testUser->id
        ]);

        $this->assertArrayHasKey('owner', $data['folder']);

        $folder = $data['folder'];

        // check if initial and found models are equal
        $this->compare($this->testFolder, $folder);
    }

    /**
     * Test API Mutation – Create new folder and find it
     *
     * Create new folder and find it with GraphQl requests
     */
    public function testCreateAndFindFolder()
    {
        $folderId = (string) new ObjectId();

        $data = $this->sendGraphql(GraphQl::MUTATION, 'CreateNewFolder', [
            'id' => $folderId,
            'ownerId' => $this->testUser->id,
            'title' => 'test folder',
            'dtCreate' => 1517651704,
            'dtModify' => 1517651704,
            'isShared' => false,
            'isRemoved' => false
        ]);

        $createdFolder = $data['folder'];

        $this->assertArrayHasKey('owner', $createdFolder);

        $data = $this->sendGraphql(GraphQl::QUERY, 'GetFolder', [
            'id' => $folderId,
            'ownerId' => $this->testUser->id
        ]);

        $foundFolder = $data['folder'];

        $this->assertArrayHasKey('owner', $foundFolder);

        // check if initial and found models are equal
        $this->assertEquals($createdFolder['id'], $foundFolder['id']);
        $this->assertEquals($createdFolder['title'], $foundFolder['title']);
        $this->assertEquals($createdFolder['owner']['id'], $foundFolder['owner']['id']);
        $this->assertEquals($createdFolder['dtCreate'], $foundFolder['dtCreate']);
        $this->assertEquals($createdFolder['dtModify'], $foundFolder['dtModify']);
        $this->assertEquals($createdFolder['isShared'], $foundFolder['isShared']);
        $this->assertEquals($createdFolder['isRemoved'], $foundFolder['isRemoved']);
    }
}

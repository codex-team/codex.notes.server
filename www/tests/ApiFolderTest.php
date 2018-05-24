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

        $this->testUser = $GLOBALS['DATA']->getUserData();

        $this->testFolder = $GLOBALS['DATA']->getFolderData();
    }

    /**
     * Test API Mutation – Create new folder
     *
     * Create folder with GraphQl request
     */
    public function testCreateFolder()
    {
        $data = $this->sendGraphql(GraphQl::MUTATION, 'Folder', [
            'id' => $this->testFolder['id'],
            'ownerId' => $this->testUser['id'],
            'title' => $this->testFolder['title'],
            'dtCreate' => $this->testFolder['dtCreate'],
            'dtModify' => $this->testFolder['dtModify'],
            'isShared' => $this->testFolder['isShared'],
            'isRemoved' => $this->testFolder['isRemoved']
        ]);

        $this->assertArrayNotHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('folder', $data);
        $this->assertArrayHasKey('owner', $data['folder']);

        $this->assertEquals($this->testFolder['id'], $data['folder']['id']);
        $this->assertEquals($this->testFolder['title'], $data['folder']['title']);
        $this->assertEquals($this->testUser['id'], $data['folder']['owner']['id']);

        $this->testFolder = $data['folder'];
    }

    /**
     * Test API Query – Get Folder
     */
    public function testGetFolder()
    {
        $data = $this->sendGraphql(GraphQl::QUERY, 'Folder', [
            'id' => $this->testFolder['id'],
            'ownerId' => $this->testUser['id']
        ]);

        $this->assertArrayNotHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('folder', $data);
        $this->assertArrayHasKey('owner', $data['folder']);

        $this->assertEquals($this->testFolder['id'], $data['folder']['id']);
        $this->assertEquals($this->testFolder['title'], $data['folder']['title']);
        $this->assertEquals($this->testUser['id'], $data['folder']['owner']['id']);

        $this->testFolder = $data['folder'];
    }

    /**
     * Test API Mutation – Update Folder's title
     *
     * Create folder with GraphQl request and find it with model
     */
    public function testUpdateFolder()
    {
        $folderNewTitle = 'renamed folder';

        $data = $this->sendGraphql(GraphQl::MUTATION, 'Folder', [
            'id' => $this->testFolder['id'],
            'ownerId' => $this->testUser['id'],
            'title' => $folderNewTitle,
            'dtCreate' => $this->testFolder['dtCreate'],
            'dtModify' => $this->testFolder['dtModify'] + 1,
            'isShared' => $this->testFolder['isShared'],
            'isRemoved' => $this->testFolder['isRemoved']
        ]);

        $this->assertArrayNotHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('folder', $data);
        $this->assertArrayHasKey('owner', $data['folder']);

        $this->assertEquals($this->testFolder['id'], $data['folder']['id']);
        $this->assertEquals($folderNewTitle, $data['folder']['title']);
        $this->assertEquals($this->testUser['id'], $data['folder']['owner']['id']);

        $this->testFolder = $data['folder'];
    }
}

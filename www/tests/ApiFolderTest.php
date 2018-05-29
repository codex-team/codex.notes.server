<?php

namespace App\Tests;

use App\Tests\Helpers\GraphQl;
use App\Tests\Helpers\WebTestCase;

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

        $this->testUser = $GLOBALS['VIRTUAL_CLIENT_1']->getUserData();
        $this->testFolder = $GLOBALS['VIRTUAL_CLIENT_1']->getFolderData();
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
        ], $GLOBALS['VIRTUAL_CLIENT_1']->getJWT());

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
        ], $GLOBALS['VIRTUAL_CLIENT_1']->getJWT());

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
     * Test API Query – Get unexisted Folder
     */
    public function testGetUnexsitedFolderWithBadFolderId()
    {
        $data = $this->sendGraphql(GraphQl::QUERY, 'Folder', [
            'id' => '000000000000000000000000',
            'ownerId' => $this->testUser['id']
        ], $GLOBALS['VIRTUAL_CLIENT_1']->getJWT());


        $this->assertArrayHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('folder', $data);
        $this->assertEmpty($data['folder']['id']);
        $this->assertEmpty($data['folder']['title']);
    }

    /**
     * Test API Query – Get unexisted Folder
     */
    public function testGetUnexsitedFolderWithBadUserId()
    {
        $data = $this->sendGraphql(GraphQl::QUERY, 'Folder', [
            'id' => $this->testFolder['id'],
            'ownerId' => '000000000000000000000000'
        ], $GLOBALS['VIRTUAL_CLIENT_1']->getJWT());


        $this->assertArrayHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('folder', $data);
        $this->assertEmpty($data['folder']['id']);
        $this->assertEmpty($data['folder']['title']);
    }

    /**
     * Test API Query – Get unexisted Folder
     */
    public function testGetUnexsitedFolderWithBadUserIdAndFolderId()
    {
        $data = $this->sendGraphql(GraphQl::QUERY, 'Folder', [
            'id' => '000000000000000000000000',
            'ownerId' => '000000000000000000000000'
        ], $GLOBALS['VIRTUAL_CLIENT_1']->getJWT());


        $this->assertArrayHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('folder', $data);
        $this->assertEmpty($data['folder']['id']);
        $this->assertEmpty($data['folder']['title']);
    }

    /**
     * Test API Query – Get not own Folder
     */
    public function testGetNotOwnFolder()
    {
        /**
         * Use second User's JWT who has no access to this folder
         */
        $jwtUser2 = $GLOBALS['VIRTUAL_CLIENT_2']->getJWT();

        $data = $this->sendGraphql(GraphQl::QUERY, 'Folder', [
            'id' => $this->testFolder['id'],
            'ownerId' => $this->testUser['id']
        ], $jwtUser2);

        $this->assertArrayHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('folder', $data);

        $this->assertEquals(null, $data['folder']);
    }

    /**
     * Test API Mutation – Update Folder's title
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
        ], $GLOBALS['VIRTUAL_CLIENT_1']->getJWT());

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

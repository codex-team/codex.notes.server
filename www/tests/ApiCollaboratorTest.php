<?php

namespace App\Tests;

use App\Components\Api\Models\Collaborator;
use App\Components\Api\Models\Folder;
use App\Tests\Helpers\GraphQl;
use App\Tests\Helpers\WebTestCase;
use MongoDB\BSON\ObjectId;

/**
 * Class ApiCollaboratorTest
 *
 * @package App\Tests
 *
 * Test GraphQl Collaborator API endpoint
 */
class ApiCollaboratorTest extends WebTestCase
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

    public function testInviteCollaborator()
    {
        $testFolderData = $this->testFolder;
        $testFolderData['id'] = (string) new ObjectId();

        $folder = new Folder($this->testUser['id']);
        $folder->sync($testFolderData);

        $secondUser = $GLOBALS['DATA_2']->getUserData();

        $data = $this->sendGraphql(GraphQl::MUTATION, 'Invite', [
            'id' => (string) new ObjectId(),
            'ownerId' => $this->testUser['id'],
            'folderId' => $folder->id,
            'email' => $secondUser['email']
        ], $GLOBALS['DATA']->getJWT());

        $this->assertArrayNotHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('invite', $data);
        $this->assertArrayHasKey('token', $data['invite']);

        $token = $data['invite']['token'];
        $this->assertNotNull($token);

        $this->assertEquals($secondUser['email'], $data['invite']['email']);

        $this->assertArrayHasKey('folder', $data['invite']);
        $this->assertEquals($folder->id, $data['invite']['folder']['id']);


        /**
         * Test Joining
         */
        $data = $this->sendGraphql(GraphQl::MUTATION, 'Join', [
            'userId' => $secondUser['id'],
            'ownerId' => $this->testUser['id'],
            'folderId' => $folder->id,
            'token' => $token
        ], $GLOBALS['DATA_2']->getJWT());

        $this->assertArrayNotHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('join', $data);
        $this->assertNotNull($data['join']);

        $this->assertArrayHasKey('email', $data['join']);
        $this->assertEquals($secondUser['email'], $data['join']['email']);

        /**
         * Check shared folder
         */
        $originFolderWithCollaborators = new Folder($this->testUser['id'], $folder->id);
        $this->assertEquals($originFolderWithCollaborators->isShared, false);

        $folderWithCollaborators = new Folder($secondUser['id'], $folder->id);
        $this->assertEquals($folderWithCollaborators->isShared, true);

        $sameFields = ['id', 'title', 'dtModify', 'dtCreate', 'isRemoved'];
        foreach ($sameFields as $field) {
            $this->assertEquals($originFolderWithCollaborators->$field, $folderWithCollaborators->$field);
        }

        $folderWithCollaborators->fillCollaborators();
        $this->assertNotNull($folderWithCollaborators->collaborators);

        /**
         * Check collaborators list
         */
        $collaboratorsIdsArray = [];
        foreach ($folderWithCollaborators->collaborators as $collaborator) {
            $collaboratorsIdsArray[] = $collaborator->userId;
        }

        $this->assertEquals(true, $this->testUser['id'] == $collaboratorsIdsArray[0]);
        $this->assertEquals(true, $secondUser['id'] == $collaboratorsIdsArray[1]);
    }
}

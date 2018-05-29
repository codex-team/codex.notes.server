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

        $this->testUser = $GLOBALS['VIRTUAL_CLIENT_1']->getUserData();
        $this->testFolder = $GLOBALS['VIRTUAL_CLIENT_1']->getFolderData();
    }

    /**
     * Collaborator invite and join
     *
     * @throws \App\Components\Base\Models\Exceptions\CollaboratorException
     * @throws \App\Components\Base\Models\Exceptions\FolderException
     */
    public function testInviteCollaborator()
    {
        /**
         * Prepare folder to be shared
         */
        $testFolderData = $this->testFolder;
        $testFolderData['id'] = (string) new ObjectId();

        /**
         * Get Folder model
         */
        $folder = new Folder($this->testUser['id']);
        $folder->sync($testFolderData);

        /**
         * Get second user data
         */
        $secondUser = $GLOBALS['VIRTUAL_CLIENT_2']->getUserData();

        /**
         * Send GraphQL Invite query
         */
        $data = $this->sendGraphql(GraphQl::MUTATION, 'Invite', [
            'id' => (string) new ObjectId(),
            'ownerId' => $this->testUser['id'],
            'folderId' => $folder->id,
            'email' => $secondUser['email']
        ], $GLOBALS['VIRTUAL_CLIENT_1']->getJWT());

        /**
         * Check for error and data fields
         */
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
         * Test Joining Folder
         */
        $data = $this->sendGraphql(GraphQl::MUTATION, 'Join', [
            'userId' => $secondUser['id'],
            'ownerId' => $this->testUser['id'],
            'folderId' => $folder->id,
            'token' => $token
        ], $GLOBALS['VIRTUAL_CLIENT_2']->getJWT());

        /**
         * Check for error and data fields
         */
        $this->assertArrayNotHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('join', $data);
        $this->assertNotNull($data['join']);

        $this->assertArrayHasKey('email', $data['join']);
        $this->assertEquals($secondUser['email'], $data['join']['email']);

        /**
         * Check Shared Folder data
         */

        /**
         * Get origin Folder model
         */
        $originFolderWithCollaborators = new Folder($this->testUser['id'], $folder->id);
        $this->assertEquals($originFolderWithCollaborators->isShared, false);

        /**
         * Get User's shared Folder
         */
        $folderWithCollaborators = new Folder($secondUser['id'], $folder->id);
        $this->assertEquals($folderWithCollaborators->isShared, true);

        /**
         * Check for same fields of shared and original fields
         */
        $sameFields = ['id', 'title', 'dtModify', 'dtCreate', 'isRemoved'];
        foreach ($sameFields as $field) {
            $this->assertEquals($originFolderWithCollaborators->$field, $folderWithCollaborators->$field);
        }

        /**
         * Fill collaborators
         */
        $folderWithCollaborators->fillCollaborators();
        $this->assertNotNull($folderWithCollaborators->collaborators);

        /**
         * Check collaborators list
         */
        $collaboratorsIdsArray = [];
        foreach ($folderWithCollaborators->collaborators as $collaborator) {
            $collaboratorsIdsArray[] = $collaborator->userId;
        }

        /**
         * Check if Users in a list of Collaborators
         */
        $this->assertEquals(true, $this->testUser['id'] == $collaboratorsIdsArray[0]);
        $this->assertEquals(true, $secondUser['id'] == $collaboratorsIdsArray[1]);
    }
}

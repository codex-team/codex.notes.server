<?php

namespace App\Tests;

use App\Components\Api\Models\Collaborator;
use App\Components\Api\Models\Folder;
use App\Components\Api\Models\User;
use App\Components\Base\Models\Mongo;
use App\System\Config;
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
    private $testCollaborator;
    private $testFolder;

    /**
     * Setup testing environment
     */
    public function setup()
    {
        parent::setup();
        $this->dropDb();
        $this->initDb();
    }

    /**
     * Initialize database with test data
     */
    private function initDb()
    {
        $this->testUser = new User();
        $this->testUser->sync([
            'name' => 'JohnDoe',
            'email' => 'JohnDoe@ifmo.su',
            'dtReg' => 1517651704
        ]);

        $id = (string) new ObjectId();
        $this->testFolder = new Folder($this->testUser->id);
        $this->testFolder->sync([
            'id' => $id,
            'title' => 'new folder',
            'dtCreate' => 1517651704,
            'dtModify' => 1517651704,
            'isShared' => false,
            'isRemoved' => false
        ]);

        $id = (string) new ObjectId();
        $collaboratorsData = [
            'id' => $id,
            'email' => 'JaneDoe@ifmo.su',
            'folderId' => $this->testFolder->id,
            'ownerId' => $this->testUser->id,
            'dtInvite' => 1517651704
        ];

        $collaboratorsData['token'] = Collaborator::getInvitationToken($collaboratorsData['ownerId'],
                                                                       $collaboratorsData['folderId'],
                                                                       $collaboratorsData['email']);

        $this->testCollaborator = new Collaborator($this->testFolder);
        $this->testCollaborator->sync($collaboratorsData);
    }

    /**
     * Drop test collections from test database
     */
    private function dropDb()
    {
        Mongo::connect(null)->dropDatabase(Config::get('MONGO_DBNAME'));
    }

    /**
     * Test API Mutation – Invite Collaborator
     *
     * Invite Collaborator with GraphQl request and find him with model
     */
    public function testInviteMutation()
    {

        if (empty(Config::get('MAILER_SERVER'))) {
            $this->markTestSkipped('MAILER_SERVER is not set. Skipped.');
        }

        $data = $this->sendGraphql(GraphQl::MUTATION, 'CollaboratorInvite', [
            'id' => (string) new ObjectID(),
            'folderId' => $this->testFolder->id,
            'ownerId' => $this->testUser->id,
            'email' => 'JamesDoe@ifmo.su',
            'dtInvite' => 1517651704
        ]);

        $invite = $data['invite'];
        $model = new Collaborator($this->testFolder, $invite['token']);

        // check if initial and saved models are equal
        $this->assertEquals($model->id, $invite['id']);
        $this->assertEquals($model->email, $invite['email']);
        $this->assertEquals($model->token, $invite['token']);
        $this->assertEquals($model->folder->id, $invite['folder']['id']);
    }

    /**
     * Test API Mutation – Invite Collaborator and find him
     *
     * Create new collaborator and find him with GraphQl requests
     */
    public function testInviteCollaboratorAndFind()
    {

        if (empty(Config::get('MAILER_SERVER'))) {
            $this->markTestSkipped('MAILER_SERVER is not set. Skipped.');
        }

        $collaboratorId = (string) new ObjectId();
        $email = 'JamesDoe@ifmo.su';

        $createdCollaborator = $this->sendGraphql(GraphQl::MUTATION, 'CollaboratorInvite', [
            'id' => $collaboratorId,
            'folderId' => $this->testFolder->id,
            'ownerId' => $this->testUser->id,
            'email' => $email,
            'dtInvite' => 1517651704
        ]);

        $foundCollaborator = $this->sendGraphql('query', 'GetCollaborator', [
            'ownerId' => $this->testUser->id,
            'folderId' => $this->testFolder->id,
            'token' => Collaborator::getInvitationToken($this->testUser->id, $this->testFolder->id, $email)
        ]);

        // check if initial and saved models are equal
        $this->assertEquals($createdCollaborator['invite']['id'], $foundCollaborator['collaborator']['id']);
    }

    /**
     * Test API Query – Find Collaborator
     *
     * Find Collaborator with GraphQl
     */
    public function testFindCollaborator()
    {
        $invitationToken = Collaborator::getInvitationToken($this->testUser->id,
                                                            $this->testFolder->id,
                                                            $this->testCollaborator->email);

        $data = $this->sendGraphql(GraphQl::QUERY, 'GetCollaborator', [
            'ownerId' => $this->testUser->id,
            'folderId' => $this->testFolder->id,
            'token' => $invitationToken
        ]);

        $this->assertEquals($this->testCollaborator->id, $data['collaborator']['id']);
    }

    /**
     * Test API Query – Find unexisting Collaborator
     *
     * Find unexisting Collaborator with GraphQl
     */
    public function testCollaboratorNotFoundQuery()
    {
        $data = $this->sendGraphql(GraphQl::QUERY, 'GetCollaborator', [
            'ownerId' => '0000',
            'folderId' => '0000',
            'token' => '0000'
        ]);

        $this->assertEmpty($data['collaborator']['id']);
    }
}

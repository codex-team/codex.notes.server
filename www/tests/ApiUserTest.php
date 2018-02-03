<?php

namespace App\Tests;

use App\Components\Api\Models\User;
use App\Components\Base\Models\Mongo;
use App\Tests\Helpers\GraphQl;
use App\Tests\Helpers\WebTestCase;
use App\Tests\Models\UsersModel;
use MongoDB\BSON\ObjectId;

/**
 * Class ApiUserTest
 *
 * @package App\Tests
 *
 * Test GraphQl user API endpoint
 */
class ApiUserTest extends WebTestCase
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
     * Drop user collection from test database
     */
    private function dropCollection()
    {
        Mongo::connect()
            ->{UsersModel::getCollectionName()}
            ->drop();
    }

    /**
     * Test API Mutation – Create new user
     *
     * Create new user with GraphQl request and find him with model
     */
    public function testCreateNewUser()
    {
        $userId = (string) new ObjectId();

        $data = $this->sendGraphql('mutation', 'CreateNewUser', [
            'id' => $userId,
            'name' => 'John Doe',
            'email' => 'jonny.codex@ifmo.su',
            'dtReg' => 1517651704
        ]);

        $user = $data['user'];
        $userModel = new User($userId);

        // check if initial and saved models are equal
        $this->assertEquals($userModel->id, $user['id']);
        $this->assertEquals($userModel->name, $user['name']);
        $this->assertEquals($userModel->email, $user['email']);
        $this->assertEquals($userModel->dtReg, $user['dtReg']);
    }

    /**
     * Test API Mutation – Create new user and find him
     *
     * Create new user and find him with GraphQl requests
     */
    public function testCreateNewUserAndFind()
    {
        $userId = (string) new ObjectId();

        $createdUser = $this->sendGraphql(GraphQl::MUTATION, 'CreateNewUser', [
            'id' => $userId,
            'name' => 'John Doe',
            'email' => 'jonny.codex@ifmo.su',
            'dtReg' => 1517651704
        ]);

        $foundUser = $this->sendGraphql('query', 'GetUser', [
            'id' => $userId
        ]);

        // check if initial and saved models are equal
        $this->assertEquals($createdUser['user']['id'], $foundUser['user']['id']);
    }

    /**
     * Test API Query – Find user
     *
     * Create new user with model and find him with GraphQl
     */
    public function testFindUser()
    {
        // save new user to DB by model
        $newUser = new UsersModel((string) new ObjectId(), 'testFindUser', 'testFindUser@ifmo.su', 123);

        $data = $this->sendGraphql(GraphQl::QUERY, 'GetUser', [
            'id' => $newUser->id
        ]);

        $this->assertEquals($newUser->id, $data['user']['id']);
    }

    /**
     * Test API Query – Find unexisting user
     *
     * Find unexisting user with GraphQl
     */
    public function testUserNotFoundQuery()
    {
        $data = $this->sendGraphql(GraphQl::QUERY, 'GetUser', [
            'id' => '000000000000000000000000'
        ]);

        $this->assertEmpty($data['user']['id']);
    }
}

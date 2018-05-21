<?php

namespace App\Tests;

use App\Components\Api\Models\User;
use App\Components\Base\Models\Mongo;
use App\Tests\Helpers\GraphQl;
use App\Tests\Helpers\WebTestCase;
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
    private $testUser;

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
     * Initialize database with test user
     */
    private function initDb()
    {
        $this->testUser = new User();
        $this->testUser->sync([
            'name' => 'JohnDoe',
            'email' => 'JohnDoe@ifmo.su',
            'dtReg' => 1517651704
        ]);
    }

    /**
     * Drop user collection from test database
     */
    private function dropCollection()
    {
        Mongo::connect()
            ->{User::getCollectionName()}
            ->drop();
    }

    /**
     * Test User Model – find existing user
     *
     * Check that model can find user in DB by ID
     */
    public function testUserModel()
    {
        $userId = $this->testUser->id;

        $user = new User($userId);

        $this->assertEquals($user->id, $this->testUser->id);
    }

    /**
     * Test API Mutation – Create new user
     *
     * Create new user with GraphQl request and find him with model
     */
    public function testCreateNewUser()
    {
        $userId = (string) new ObjectID();

        $data = $this->sendGraphql(GraphQL::MUTATION, 'CreateNewUser', [
            'id' => $userId,
            'name' => 'JohnDoe',
            'email' => 'JohnDoe@ifmo.su'
        ]);

        $user = $data['user'];
        $userModel = new User($userId);

        // check if initial and saved models are equal
        $this->assertEquals($userModel->id, $user['id']);
        $this->assertEquals($userModel->name, $user['name']);
        $this->assertEquals($userModel->email, $user['email']);
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
            'name' => 'JohnDoe',
            'email' => 'JohnDoe@ifmo.su'
        ]);

        $foundUser = $this->sendGraphql(GraphQl::QUERY, 'GetUser', [
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
        $data = $this->sendGraphql(GraphQl::QUERY, 'GetUser', [
            'id' => $this->testUser->id
        ]);

        $this->assertEquals($this->testUser->id, $data['user']['id']);
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

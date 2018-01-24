<?php

namespace App\Tests;

use App\Components\Api\Models\User;
use App\Components\Base\Models\Mongo;
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
        $userId = new ObjectId();

        // create new user with GraphQl request
        $userMutation = UsersModel::getCreateNewUserMutation((string) $userId, 'testCreateNewUser', 'testCreateNewUser@ifmo.su', 123);
        $output = $this->client->post('/graphql', $userMutation);

        // check if response is not forbidden
        $this->assertFalse($this->client->response->isForbidden(), 'Auth Error (403).');

        $data = json_decode($output, true);

        // check json ouput structure
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('user', $data['data']);

        $user = $data['data']['user'];

        // get User from DB by model
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
        $userId = new ObjectId();

        // create new user with GraphQl request
        $userMutation = UsersModel::getCreateNewUserMutation((string) $userId, 'testCreateNewUserAndFind', 'testCreateNewUserAndFind@ifmo.su', 123);
        $output = $this->client->post('/graphql', $userMutation);
        $data = json_decode($output, true);

        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('user', $data['data']);

        $createdUser = $data['data']['user'];

        // get user by Id with GraphQl request
        $userQuery = UsersModel::getFindUserQuery($userId);
        $output = $this->client->post('/graphql', $userQuery);

        $data = json_decode($output, true);

        // check json ouput structure
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('user', $data['data']);

        $foundUser = $data['data']['user'];

        // check if initial and saved models are equal
        $this->assertEquals($createdUser['id'], $foundUser['id']);
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

        // get user by Id with GraphQl request
        $userQuery = UsersModel::getFindUserQuery($newUser->id);
        $output = $this->client->post('/graphql', $userQuery);

        $data = json_decode($output, true);

        // check json ouput structure
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('user', $data['data']);

        $user = $data['data']['user'];

        $this->assertEquals($newUser->id, $user['id']);
    }

    /**
     * Test API Query – Find unexisting user
     *
     * Find unexisting user with GraphQl
     */
    public function testUserNotFoundQuery()
    {
        // try to find unexisting user in DB
        $userQuery = UsersModel::getFindUserQuery("000000000000000000000000");
        $output = $this->client->post('/graphql', $userQuery);

        $data = json_decode($output, true);

        // check json ouput structure
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('user', $data['data']);

        $this->assertEmpty($data['data']['user']['id']);
    }
}

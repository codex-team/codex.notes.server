<?php

namespace App\Tests;

use App\Components\Api\Models\User;
use App\Components\Base\Models\Mongo;
use App\Tests\Helpers\WebTestCase;
use App\Tests\Models\UsersModel;
use MongoDB\BSON\ObjectId;

/**
 * Class ApiUserTest
 * @package App\Tests
 *
 * Test GraphQl user API endpoint
 */
class ApiUserTest extends WebTestCase
{
    public function setup()
    {
        parent::setup();
        $this->dropCollection();
    }

    private function dropCollection()
    {
        Mongo::connect()
            ->{UsersModel::getCollectionName()}
            ->drop();
    }

    /**
     * Test API Mutation – Create new user
     *
     * Create new user with GraphQl request and find it with model
     */
    public function testCreateNewUser()
    {
        $userId = new ObjectId();

        $userMutation = UsersModel::getCreateNewUserMutation((string)$userId, 'testCreateNewUser', 'testCreateNewUser@ifmo.su', 123);
        $output = $this->client->post('/graphql', $userMutation);

        $this->assertFalse($this->client->response->isForbidden(), 'Auth Error (403).');

        $data = json_decode($output, true);

        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('user', $data['data']);

        $user = $data['data']['user'];

        $userModel = new User($userId);

        $this->assertEquals($userModel->id, $user['id']);
        $this->assertEquals($userModel->name, $user['name']);
        $this->assertEquals($userModel->email, $user['email']);
        $this->assertEquals($userModel->dtReg, $user['dtReg']);
    }

    /**
     *
     */
    public function testCreateNewUserAndFind()
    {
        $userId = new ObjectId();
        $userMutation = UsersModel::getCreateNewUserMutation((string)$userId, 'testCreateNewUserAndFind', 'testCreateNewUserAndFind@ifmo.su', 123);
        $output = $this->client->post('/graphql', $userMutation);
        $data = json_decode($output, true);

        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('user', $data['data']);

        $createdUser = $data['data']['user'];

        $userQuery = UsersModel::getFindUserQuery($userId);
        $output = $this->client->post('/graphql', $userQuery);

        $data = json_decode($output, true);
        $foundUser = $data['data']['user'];

        $this->assertEquals($createdUser['id'], $foundUser['id']);
    }

    /**
     * Test API Query – Find user
     *
     * Create new user with model and find it with GraphQl
     */
    public function testFindUser()
    {
        $newUser = new UsersModel(new ObjectId(), 'testFindUser', 'testFindUser@ifmo.su', 123);

        $userQuery = UsersModel::getFindUserQuery($newUser->id);
        $output = $this->client->post('/graphql', $userQuery);

        $data = json_decode($output, true);
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
        $userQuery = UsersModel::getFindUserQuery("000000000000000000000000");
        $output = $this->client->post('/graphql', $userQuery);

        $data = json_decode($output, true);

        $this->assertEmpty($data['data']['user']['id']);
    }
}
<?php

namespace App\Tests;

use App\Components\Api\Models\User;
use App\Components\Base\Models\Mongo;
use App\Components\OAuth\OAuth;
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
    /**
     * Setup testing environment
     */
    public function setup()
    {
        parent::setup();
    }

    /**
     * Test User Model – find existing user
     *
     * Check that model can find user in DB by ID
     */
    public function testUserModel()
    {
        $testUser = $GLOBALS['DATA']->getUserData();

        $user = new User($testUser['id']);

        $this->assertEquals($user->id, $testUser['id']);
    }

    /**
     * Test API Query – Find user
     *
     *  Get User's data with GraphQl Query
     */
    public function testQueryUser()
    {
        $testUser = $GLOBALS['DATA']->getUserData();

        $data = $this->sendGraphql(GraphQl::QUERY, 'User', [
            'id' => $testUser['id']
        ], $GLOBALS['DATA']->getJWT());

        $this->assertArrayNotHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertEquals($testUser['id'], $data['user']['id']);
    }

    /**
     * Test API Query – Find unexisting user
     *
     * Query unexisting user with GraphQl
     */
    public function testUserNotFoundQuery()
    {
        $data = $this->sendGraphql(GraphQl::QUERY, 'User', [
            'id' => '000000000000000000000000'
        ], $GLOBALS['DATA']->getJWT());

        $this->assertArrayNotHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertEmpty($data['user']['id']);
    }

    /**
     * Test API Mutation – Update User's name and email
     */
    public function testUpdateUserData()
    {
        $testUser = $GLOBALS['DATA']->getUserData();

        $newData = [
            'name' => 'Miron',
            'email' => 'miron@ifmo.su'
        ];

        $data = $this->sendGraphql(GraphQL::MUTATION, 'User', [
            'id' => $testUser['id'],
            'name' => $newData['name'],
            'email' => $newData['email']
        ], $GLOBALS['DATA']->getJWT());

        $this->assertArrayNotHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertEquals($testUser['id'], $data['user']['id']);
        $this->assertEquals($newData['name'], $data['user']['name']);
        $this->assertEquals($newData['email'], $data['user']['email']);

        $GLOBALS['DATA']->updateData($newData);
    }

    /**
     * Test API Mutation – Update another User's name and email
     *
     * User should has no access to do that
     */
    public function testUpdateAnotherUserData()
    {
        $testUser2 = $GLOBALS['DATA_2']->getUserData();

        $newData = [
            'name' => 'Miron 2',
            'email' => 'miron2@ifmo.su'
        ];

        $data = $this->sendGraphql(GraphQL::MUTATION, 'User', [
            'id' => $testUser2['id'],
            'name' => $newData['name'],
            'email' => $newData['email']
        ], $GLOBALS['DATA']->getJWT());

        $this->assertArrayHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertEquals(null, $data['user']);
    }
}

<?php

namespace App\Tests;

use App\Tests\Helpers\WebTestCase;

/**
 * Class EnvTest
 * @package Tests
 *
 * Test existence and validity of environment settings and .env file
 */
class GraphQlNaiveTest extends WebTestCase
{
    /**
     * Test if main page is accessible via HTTP GET Request
     */
    public function testCreateNewUser() {
        $data = [
            'query' => 'mutation CreateNewUser($id: ID!, $name: String!, $email: String!) {
                          user(id: $id, name: $name, email: $email) {
                            id,
                            name,
                            email
                          }
                        }',
            'variables' => [
                'id' => 1,
                'name' => 'testUser',
                'email' => 'testUser@ifmo.su'
            ],
            'operationName' => 'CreateNewUser'
        ];
        $output = $this->client->post('/graphql', $data);

        $data = json_decode($output, true);

        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('user', $data['data']);

        $user = $data['data']['user'];

        $this->assertEquals('1', $user['id']);
        $this->assertEquals('testUser', $user['name']);
        $this->assertEquals('testUser@ifmo.su', $user['email']);
    }

}
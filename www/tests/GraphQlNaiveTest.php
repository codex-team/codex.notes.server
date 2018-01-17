<?php

namespace App\Tests;

use App\System\Config;
use App\Tests\Helpers\WebTestCase;

/**
 * Class GraphQlNaiveTest
 * @package App\Tests
 *
 * Test GraphQl basic mutations
 */
class GraphQlNaiveTest extends WebTestCase
{
    /**
     * Load environment variables
     */
    public function loadEnvironment()
    {
        parent::loadEnvironment();
    }

    /**
     * Test if main page is accessible via HTTP GET Request
     */
    public function testCreateNewUser()
    {
        $data = [
            'query' => 'mutation CreateNewUser($id: ID!, $name: String!, $email: String!, $dtReg: Int!) {
                          user(id: $id, name: $name, email: $email, dtReg: $dtReg) {
                            id,
                            name,
                            email,
                            dtReg
                          }
                        }',
            'variables' => [
                'id' => "56732d3dda14d81214634921",
                'name' => 'testUser',
                'email' => 'testUser@ifmo.su',
                'dtReg' => 123
            ],
            'operationName' => 'CreateNewUser'
        ];
        $output = $this->client->post('/graphql', $data);

        $this->assertFalse($this->client->response->isForbidden(), 'Auth Error (403).');

        $data = json_decode($output, true);

        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('user', $data['data']);

        $user = $data['data']['user'];

        $this->assertEquals('56732d3dda14d81214634921', $user['id']);
        $this->assertEquals('testUser', $user['name']);
        $this->assertEquals('testUser@ifmo.su', $user['email']);
        $this->assertEquals('123', $user['dtReg']);
    }
}
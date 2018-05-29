<?php

namespace App\Tests;

use App\Tests\Helpers\GraphQl;
use App\Tests\Helpers\WebTestCase;

/**
 * Class ApiNoteTest
 *
 * @package App\Tests
 *
 * Test GraphQl Note API endpoint
 */
class ApiNoteTest extends WebTestCase
{
    private $testUser;
    private $testFolder;
    private $testNote;

    /**
     * Setup testing environment
     */
    public function setup()
    {
        parent::setup();

        $this->testUser = $GLOBALS['DATA']->getUserData();
        $this->testFolder = $GLOBALS['DATA']->getFolderData();
        $this->testNote = $GLOBALS['DATA']->getNoteData();
    }

    /**
     * Test API Mutation – Create new note
     *
     * Create note with GraphQl request
     */
    public function testCreateNote()
    {
        $data = $this->sendGraphql(GraphQl::MUTATION, 'Note', [
            'id' => $this->testNote['id'],
            'authorId' => $this->testUser['id'],
            'folderId' => $this->testFolder['id'],
            'title' => $this->testNote['title'],
            'content' => $this->testNote['content'],
            'dtCreate' => $this->testNote['dtCreate'],
            'dtModify' => $this->testNote['dtModify'],
            'isRemoved' => $this->testNote['isRemoved']
        ], $GLOBALS['DATA']->getJWT());

        $this->assertArrayNotHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('note', $data);
        $this->assertArrayHasKey('author', $data['note']);

        $this->assertEquals($this->testNote['id'], $data['note']['id']);
        $this->assertEquals($this->testNote['title'], $data['note']['title']);
        $this->assertEquals($this->testUser['id'], $data['note']['author']['id']);

        $this->testNote = $data['note'];
    }

    /**
     * Test API Query – Get Note
     */
    public function testGetNote()
    {
        $data = $this->sendGraphql(GraphQl::QUERY, 'Note', [
            'id' => $this->testNote['id'],
            'authorId' => $this->testUser['id'],
            'folderId' => $this->testFolder['id']
        ], $GLOBALS['DATA']->getJWT());

        $this->assertArrayNotHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('note', $data);
        $this->assertArrayHasKey('author', $data['note']);

        $this->assertEquals($this->testNote['id'], $data['note']['id']);
        $this->assertEquals($this->testNote['title'], $data['note']['title']);
        $this->assertEquals($this->testUser['id'], $data['note']['author']['id']);

        $this->testNote = $data['note'];
    }

    /**
     * Test API Query – Get unexisted Note
     */
    public function testGetUnexistedNote()
    {
        $data = $this->sendGraphql(GraphQl::QUERY, 'Note', [
            'id'       => '000000000000000000000000',
            'authorId' => $this->testUser['id'],
            'folderId' => $this->testFolder['id']
        ], $GLOBALS['DATA']->getJWT());

        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('note', $data);
        $this->assertEmpty($data['note']['id']);
        $this->assertEmpty($data['note']['title']);
    }

    /**
     * Test API Query – Get not own Note
     */
    public function testGetNotOwnNote()
    {
        /**
         * Use second User's JWT who has no access to this note
         */
        $jwtUser2 = $GLOBALS['DATA_2']->getJWT();

        $data = $this->sendGraphql(GraphQl::QUERY, 'Note', [
            'id' => $this->testNote['id'],
            'authorId' => $this->testUser['id'],
            'folderId' => $this->testFolder['id']
        ], $jwtUser2);

        $this->assertArrayHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('note', $data);

        $this->assertEquals(null, $data['note']);
    }

    /**
     * Test API Mutation – Update Note's title and content
     */
    public function testUpdateNote()
    {
        $noteNewTitle = 'New title';
        $noteNewContent = '[{\"type\":\"paragraph\",\"data\":{\"text\":\"<p>You are amazing!</p>\",\"format\":\"html\",\"introText\":\"<<same>>\"}}]';

        $data = $this->sendGraphql(GraphQl::MUTATION, 'Note', [
            'id' => $this->testNote['id'],
            'authorId' => $this->testUser['id'],
            'folderId' => $this->testFolder['id'],
            'title' => $noteNewTitle,
            'content' => $noteNewContent,
            'dtCreate' => $this->testNote['dtCreate'],
            'dtModify' => $this->testNote['dtModify'] + 1,
            'isRemoved' => $this->testNote['isRemoved']
        ], $GLOBALS['DATA']->getJWT());

        $this->assertArrayNotHasKey('errors', $data);
        $this->assertArrayHasKey('data', $data);
        $data = $data['data'];

        $this->assertArrayHasKey('note', $data);
        $this->assertArrayHasKey('author', $data['note']);

        $this->assertEquals($this->testNote['id'], $data['note']['id']);
        $this->assertEquals($noteNewTitle, $data['note']['title']);
        $this->assertEquals($noteNewContent, $data['note']['content']);
        $this->assertEquals($this->testUser['id'], $data['note']['author']['id']);

        $this->testNote = $data['note'];
    }
}

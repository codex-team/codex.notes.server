<?php

namespace App\Tests;

use App\Components\Base\Models\Mongo;
use App\Tests\Helpers\WebTestCase;
use App\Tests\Models\UsersModel;

/**
 * Class ApiCollaboratorTest
 *
 * @package App\Tests
 *
 * Test GraphQl user API endpoint
 */
class ApiCollaboratorTest extends WebTestCase
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

//    public function testQueryFolder()
//    {
//        $userId = (string) new ObjectId();
//        $folderId = (string) new ObjectId();
//        $userIdToInvite = (string) new ObjectId();
//
//        $newUser = new UsersModel($userId, 'testFindUser', 'testFindUser@ifmo.su', 123);
//        $userToInvite = new UsersModel($userIdToInvite, 'userToInvite', '3285b08cb2-87bb61@inbox.mailtrap.io', 123);
//
//        $newFolder = new FoldersModel($userId, $folderId, 'new folder', []);
//
//        $folderQuery = FoldersModel::getFindFolderQuery((string) $folderId, (string) $userId);
//        $output = $this->client->post('/graphql', $folderQuery);
//        $data = json_decode($output, true);
//
//        $collaboratorMutation = CollaboratorsModel::getCreateNewCollaboratorMutation((string) $userIdToInvite, (string) $userId, (string) $folderId, 'userToInvite@ifmo.su', 123, false);
//
//        $folderMutation = FoldersModel::getCreateNewUserMutation((string) $folderId, (string) $userId, 'new folder', 123, 123, false, false);
//        $output = $this->client->post('/graphql', $collaboratorMutation);
//        $data = json_decode($output, true);
//    }
}

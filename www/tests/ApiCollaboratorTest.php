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
}

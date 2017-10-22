<?php

namespace App\Versions\V1;

use App\Versions\V1\Api\User as ApiUser;
use App\System\HTTP;
use App\System\Log;

class Api
{
    /**
     * @var string
     */
    protected $version = 'v1';

    /**
     * @var object App\System\Log
     */
    protected $logger;
    
    /**
     * Init API
     */
    function __construct(string $version = 'v1')
    {
        if (!$this->logger) {
            $this->logger = new Log();
        }

        $this->version = $version;
    }

    public function getUser()
    {
        return new ApiUser();
    }
}
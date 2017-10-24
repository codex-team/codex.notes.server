<?php

namespace App\Versions\V1;

use App\Versions\V1\Api\User as ApiUser;
use App\System\HTTP;
use App\System\Log;

/**
 * Class Api
 * Оболочка, через которую взаимодействуют контроллеры с моделями.
 *
 * @package App\Versions\V1
 */
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

    /**
     * Получаем объект API класса для работы с моделью User
     * @return ApiUser
     */
    public function getUser()
    {
        return new ApiUser();
    }
}
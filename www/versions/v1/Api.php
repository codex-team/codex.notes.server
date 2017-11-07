<?php

namespace App\Versions\V1;

use App\Versions\V1\Api\User as ApiUser;
use App\Versions\V1\Api\Folder as ApiFolder;
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
     * @var object App\System\Log
     */
    protected $logger;

    /**
     * Contain API response
     * @var array
     */
    protected $response;
    
    /**
     * Init API
     */
    public function __construct()
    {
        if (!$this->logger) {
            $this->logger = new Log();
        }

        $this->response = $this->getDefaultResponseAsArray();
    }

    /**
     * Получаем объект API класса для работы с моделью User
     * @return ApiUser
     */
    public function getUser()
    {
        return new ApiUser();
    }

    public  function getFolder()
    {
        return new ApiFolder();
    }

    public function getResponse()
    {
        return [
            'code' => HTTP::CODE_SUCCESS,
            'success' => true,
            'result' => $this->response
        ];
    }

    public function getDefaultResponseAsArray()
    {
        return [
            'code' => HTTP::CODE_SUCCESS,
            'success' => true,
            'result' => null
        ];
    }
}
<?php

namespace App\Versions\V1;

use App\Versions\V1\Api\User as ApiUser;
use App\Versions\V1\Api\Folder as ApiFolder;
use App\System\HTTP;
use App\System\Log;
use App\Versions\V1\Models\Handlers\AppExceptionHandler;

/**
 * Class Api
 * Оболочка, через которую взаимодействуют контроллеры с моделями.
 *
 * @package App\Versions\V1
 *
 * @deprecated Use GraphQL-scheme with App\Components\Api\Api
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

    /**
     * Получаем объект класса API Folder для работы с его методами
     * @return App\Versions\V1\Api\Folder()
     */
    public function getFolder()
    {
        return new ApiFolder();
    }

    /**
     * Получаем результат работы API
     * Вызывается самой последней
     * @return array
     */
    public function getResponse()
    {
        $_response = $this->getDefaultResponseAsArray();
        $_response['result'] = $this->response;
        return $_response;
    }

    /**
     * Используется в ExceptionHandler'ах и $this->getResponse()
     * Нам неважно, какой `result`, он перезапишется
     * `result` key обязательный
     * @return array
     */
    public function getDefaultResponseAsArray()
    {
        return [
            'code' => HTTP::CODE_SUCCESS,
            'success' => true,
            'result' => null
        ];
    }
}
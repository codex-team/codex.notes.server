<?php

namespace App\Modules;

use App\Modules\HTTP;
use App\Modules\Api\User;

/**
 * @method void setVersion()
 */
class Api
{
    /**
     * Used in routes /v{$version}/<...>
     * @var string
     */
    protected $version = 'v1';
    
    /**
     * Init API
     */
    function __construct()
    {
        $this->setVersion();
    }

    /**
     * Set API version
     */
    private function setVersion(string $version = 'v1')
    {
        global $logger, $messages;

        if ($version) {
            $this->version = $version;
        }
        else {
            $message = $messages['api']['version']['error'];

            $logger->error($message);

            return [
                'code' => HTTP::CODE_BAD_REQUEST,
                'success' => false,
                'result' => $message
            ];        
        }
    }

    public function getUser()
    {
        return new User();
    }
}
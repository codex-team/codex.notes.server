<?php

namespace App\System;

/**
 * Class HTTP
 * Оболочка для обработки HTTP запросов
 * На данный момент хранит только коды ответов червера
 *
 * @package App\System
 */
class HTTP
{
    CONST CODE_SERVER_ERROR = 500;
    CONST CODE_NOT_ALLOWED_METHOD = 405;
    CONST CODE_NOT_FOUND = 404;
    CONST CODE_BAD_REQUEST = 400;
    CONST CODE_SUCCESS = 200;

    CONST STRING_SERVER_ERROR = 'Internal server error';
    CONST STRING_NOT_ALLOWED_METHOD = 'Method must be one of: %s';

    static public function Request($method, $url, $params=[], $headers=[])
    {

        $curl = curl_init();
        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST,1);
                if (count($params)) {
                    curl_setopt($curl,CURLOPT_POSTFIELDS, http_build_query($params));
                }
                break;
            case 'GET':
                if (!count($params)) {
                    break;
                }


                $url .= '?' . http_build_query($params);
                break;
            default:
                throw new \Exception('Unsupported method');
        }

        if (count($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;


    }

}
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
    CONST CODE_BAD_REQUEST = 400;
    CONST CODE_SUCCESS = 200;
}
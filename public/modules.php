<?php

use RKA\Middleware\IpAddress;

/**
 * Модуль позволяет получить ip запроса через $request
 */
$app->add(new IpAddress());
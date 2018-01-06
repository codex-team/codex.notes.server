<?php

use RKA\Middleware\IpAddress;

/**
 * Allow to get an IP address from $request
 */
$app->add(new IpAddress());
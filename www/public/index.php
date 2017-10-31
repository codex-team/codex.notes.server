<?php

namespace App;

use App\Versions\V1\Models\Handlers\AppExceptionHandler;
use App\Versions\V1\Models\Handlers\CodeExceptionHandler;
use App\Versions\V1\Models\Handlers\RouteExceptionHandler;
use App\Versions\V1\Models\Handlers\MethodNotAllowedExceptionHandler;

/**
 * Автоподгрузка классов Slim и приложения
 * У приложения namespace App;
 */
require '../vendor/autoload.php';

/**
 * Инициализируем приложение
 * @see \Slim\Container::$defaultSettings
 */
$app = new \Slim\App([
    'settings' => ['displayErrorDetails' => true]
]);

$c = $app->getContainer();


$c['errorHandler'] = function ($c) {
    return new AppExceptionHandler();
};

$c['notFoundHandler'] = function ($c) {
    return new RouteExceptionHandler();
};

$c['phpErrorHandler'] = function ($c) {
    return new CodeExceptionHandler();
};

$c['notAllowedHandler'] = function ($c) {
    return new MethodNotAllowedExceptionHandler();
};

/**
 *  Подключаем к $app модули
 */
require 'modules.php';

/**
 * Определяем роуты
 */
require 'routes.php';

$app->run();
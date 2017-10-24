<?php

namespace App;

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

/**
 *  Подключаем к $app модули
 */
require 'modules.php';

/**
 * Определяем роуты
 */
require 'routes.php';

$app->run();
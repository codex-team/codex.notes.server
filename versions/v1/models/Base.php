<?php

namespace App\Versions\V1\Models;

use App\System\Log;

/**
 * Class Base
 * Класс родитель для прочих объектов
 * недо-интерфейс
 *
 * @package App\Versions\V1\Models
 */
class Base {

    /**
     * Содержит объект для логирования событий
     * @var object Log
     */
    protected $logger;

    /**
     * Массив с настройками модели
     * Обычно инициализируетс в конструкторе класса
     * @var array
     */
    protected $config;

    /**
     * Храним сообщения для вывода в ответе API
     * Обычно инициализируется в конструкторе класса
     * @var array
     */
    protected $messages;

    /**
     * Инициализируем логгер
     */
    public function __construct()
    {
        if (!$this->logger) {
            $this->logger = new Log();
        }
    }
}
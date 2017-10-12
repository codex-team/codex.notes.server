<?php

namespace Models;

require_once '../modules/Mongo.php';
require_once '../modules/Auth.php';

use Models\Directory;
use Models\User;
use Modules\Mongo;

class Directory
{

    /**
     * Коллекция папок
     * @var object MongoDB\Collection
     */
    private $collection;

    /**
     * Коллекция папки
     * @var object null|MongoDB\Model\BSONDocument
     */
    private $collectionItem;

    /**
     * Имя коллекции в Mongo
     * @var string
     */
    private $collectionName = 'dir';
    
    public function __construct()
    {

    }
}
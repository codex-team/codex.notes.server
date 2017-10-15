<?php

namespace App\Models;

use App\Models\User;
use App\Modules\Mongo;

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
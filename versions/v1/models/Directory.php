<?php

namespace App\Versions\V1\Models;

class Directory extends Base;
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
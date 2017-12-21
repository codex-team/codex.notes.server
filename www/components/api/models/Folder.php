<?php

namespace App\Components\Api\Models;


class Folder
{
    public $id;
    public $title;
    public $owner;
    public $dt_create;
    public $dt_modify;
    public $is_shared;

    public function __construct($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        $this->owner =  new User($this->owner);
    }
}
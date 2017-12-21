<?php

namespace App\Components\Api\Models;


class Note
{
    public $id;
    public $title;
    public $dt_create;
    public $dt_modify;
    public $content;
    public $author;
    public $views;

    public function __construct($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        $this->author = new User($this->owner);
    }
}
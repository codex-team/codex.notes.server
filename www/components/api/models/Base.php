<?php

namespace App\components\api\models;


class Base
{
    public function __construct(){}

    /**
     * Fill model with values from data
     *
     * @param array $data
     */
    protected function fillModel(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
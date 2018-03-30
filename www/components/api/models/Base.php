<?php

namespace App\components\api\models;

class Base implements \JsonSerializable
{
    public function __construct()
    {
    }

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

    /**
     * Check if model exists in database
     *
     * @return Boolean
     */
    public function exists(): bool
    {
        return !is_null($this->id);
    }

    public function jsonSerialize()
    {
    }
}

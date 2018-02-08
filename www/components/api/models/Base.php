<?php

namespace App\components\api\models;

use App\Components\Base\Models\Mongo;

class Base
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
     * Find model data in the database by id
     *
     * @param string|null $id
     * @return Base|null
     */
    public static function find(string $id = null)
    {

        if (is_null($id)) {
            return null;
        }

        $query = [
            '_id' => $id
        ];

        $mongoResponse = Mongo::connect()
            ->{static::getCollectionName()}
            ->findOne($query);

        if (is_null($mongoResponse)) {
            return null;
        }

        $model = new self();
        $model->fillModel($mongoResponse);

        return $model;

    }
}

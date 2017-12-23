<?php

namespace App\Components\Api\Models;

/**
 * Model User
 *
 * @package App\Components\Api\Models
 */
class User
{
    /**
     * Users unique identifier
     *
     * @var int|null
     */
    public $id;

    /**
     * User's nickname
     *
     * @var string|null
     */
    public $name;

    /**
     * User's email address
     *
     * @var string|null
     */
    public $email;

    /**
     * Registration timestamp
     *
     * @var int|null
     */
    public $dt_reg;

    /**
     * Collection name
     *
     * @var string|null
     */
    private $collectionName;

    /**
     * User constructor
     *
     * @param string|null $id      if passed, returns filled User model
     */
    public function __construct(string $id = null)
    {
        $this->collectionName = self::getCollectionName();

        if ($id) {

            $this->id = $id;

            // @todo create get function
//            $this->get();
        }

    }

    /**
     * Create or update existing user
     *
     * @param array $data
     */
    public function sync(array $data)
    {
        $query = [
            'id' => $data['id']
        ];

        $update = [
            '$set' => $data
        ];

        $mongoResponse = Mongo::connect()
            ->{$this->collectionName}
            ->findOneAndUpdate($query, $update, ['upsert' => true]);

        /** mongoResponse could be NULL if no item was found */
        $this->fillModel($mongoResponse ?: $data);
    }

    /**
     * Fill model with values from data
     *
     * @param array $data
     */
    private function fillModel(array $data)
    {
        foreach ($data as $key => $value) {

            if (property_exists($this, $key)) {

                $this->$key = $value;
            }
        }
    }

    /**
     * Return collection name
     *
     * @return string
     */
    private static function getCollectionName(): string
    {
        return 'users';
    }
}
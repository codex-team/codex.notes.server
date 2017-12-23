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
     * User constructor
     *
     * @param string|null $id      if passed, returns filled User model
     */
    public function __construct(string $id = null)
    {
        $this->id = $id;
    }
}
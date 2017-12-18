<?php

namespace App\Components\Api\Models;

/**
 * Model User
 * @package App\Components\Api\Models
 */
class User
{
    /**
     * @var int|null    Users unique identifier
     */
    public $id;

    /**
     * @var string|null  User's nickname
     */
    public $name;

    /**
     * @var string|null  User's email address
     */
    public $email;

    /**
     * @var int|null Registration timestamp
     */
    public $dt_reg;

    /**
     * User constructor.
     * @param int|null $userId      If passed, returns filled User model
     */
    public function __construct(int $userId = null)
    {
        // @todo Construct collection model and get user document
    }
}
<?php

namespace App\Schema;

use App\Schema\Types\{
    Collaborator, Folder, Mutation, Note, Query, User
};

/**
 * Class Types
 *
 * @package App\Schema
 *
 * Registry of custom types for GraphQL schema
 */
class Types
{
    /**
     * GraphQL Query type
     */
    private static $query;

    /**
     * GraphQL Mutation type
     */
    private static $mutation;

    /**
     * Custom types for CodeX Notes
     */
    private static $user;
    private static $folder;
    private static $note;
    private static $collaborator;

    public static function query()
    {
        return self::$query ?: (self::$query = new Query());
    }

    public static function mutation()
    {
        return self::$mutation ?: (self::$mutation = new Mutation());
    }

    public static function user()
    {
        return self::$user ?: (self::$user = new User());
    }

    public static function folder()
    {
        return self::$folder ?: (self::$folder = new Folder());
    }

    public static function note()
    {
        return self::$note ?: (self::$note = new Note());
    }

    public static function collaborator()
    {
        return self::$collaborator ?: (self::$collaborator = new Collaborator());
    }
}

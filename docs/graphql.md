# CodeX Notes API development guide

This guide describes how to develop an API methods for the CodeX Notes.

## GraphQL

> GraphQL is a query language for your API, and a server-side runtime for executing queries by using a type system you define for your data. GraphQL isn't tied to any specific database or storage engine and is instead backed by your existing code and data.

From the [Introduction](http://graphql.org/learn/).

To better understanding the GraphQL you should know next facts:

1. Documentation describes a Query Language, not client-side or API-side implementation. They are different.
2. All works on the 2 things [Queries](http://graphql.org/learn/queries/) and the [Types](http://graphql.org/learn/schema/). Read about it.

### How it works

In a few words:

1. We should implement types for all. [Like this](http://webonyx.github.io/graphql-php/type-system/object-types/) 
2. We build schema, based on the `query` and `mutation` types. [Like this](http://webonyx.github.io/graphql-php/type-system/schema/)
3. We should expose `one` endpoint for all queries. For example `/graphql` 
4. When we get a request, we should parse it for the `query`, `variables` fields. It's [recommended](http://graphql.org/learn/serving-over-http/) format. 
5. Next we pass `schema`, `query` and `variables` to the `GraphQL::executeQuery` method and return the answer. [Like this](http://webonyx.github.io/graphql-php/getting-started/)

For simplify 4-5 steps, we use [graphql-php Standard Server](http://webonyx.github.io/graphql-php/executing-queries/#using-server).

> It supports more features out of the box, including parsing HTTP requests, producing a spec-compliant response; batched queries; persisted queries.

### How to link schema with DB

To fill any Type with data, use `resolve` field in the `Query` type definition. Or use [fieldResolver](http://webonyx.github.io/graphql-php/data-fetching/) method.

We will use Models (`components/api/models/`) for working with DB.
 
```php
<?php
class Query extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function() {
                return [
                    'user' => [
                        'type' => Types::user(),
                        'description' => 'Returns user by id',
                        'args' => [
                            'id' => Type::nonNull(Type::int()),
                        ],
                        'resolve' => function($root, $args) {
                            return new User($args['id']); // here is a DataFetching
                        }
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}

```

## Types 

We will use these types:

- `Query` _(GraphQL required)_ — describes all API requests. Connects Queries with Models.
- `Mutation` _(GraphQL required)_ — same as Query, but using for data modifications.
- `User` - describes User Model
- `Note` - describes Note Model
- `Folder` - describes Folder Model
- `Collaborator` - describes Collaborator Model

## How to add a Custom Type

- Describe Type via class in `schema/types/<TypeName>.php`
- Add new Type to the Registry (see below)
- Add new Type to the `Query` and `Mutation` types and provide model-working logic with `resolve` field. 

We use registry for custom types `schema/types.php`. 

```php
<?php

namespace App\Schema;

use App\Schema\Types\ {
    User,
    Folder,
    Note,
};

/**
 * Class Types
 * @package App\Schema
 *
 * Registry of custom types for GraphQL schema
 */
class Types
{
    /**
     * Custom types for CodeX Notes
     */
    private static $user;
    private static $folder;
    private static $note;

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
}
```

## Where to write business logic

- Place all logic in a specified Models.
- Integrate it to the `Query` or `Mutation` type.

## Structure

```
- components/
        |____ api/
                |____ models/                // all business logic here
                    |____ Folders.php
                    |____ Notes.php
                    |____ User.php
                |____ Api.php                // requests endpoint processor
                
        |____ global/                        // globals for all components
        |____ index/                         // component for the /index web page
        
- public/
        |____ static/   
            |____ css/
            |____ images/
            
        |____ index.php                     // application entrypoint
        |____ routes.php                    // Slim Router 
       
- schema/
        |____ types/                        // custom types
            |____ Collaborator.php
            |____ Folder.php
            |____ Note.php
            |____ ...
            
        |____ Types.php                     // Custom Types Registry
        
 - system/                                  // utilities

```

## Queries testing tools

Use this tools for sending queries and discover graph.

- https://insomnia.rest
- or Chrome Extension [GraphiQL](https://chrome.google.com/webstore/detail/chromeiql/fkkiamalmpiidkljmicmjfbieiclmeij)

![](https://capella.pics/03778dbd-0872-4812-aebd-ba400651fe67/resize/1300)

## How to view logs

Enter a php process and look at the `logs/log_<YYYY-MM-DD>.txt`:

```bash
docker ps  // and get container ID
docker exes -ti <Container Id> /bin/bash
tail logs/log_YYYY-MM-DD.txt
```

If `.env` contains `DEBUG=TRUE`, you will se GraphQL errors in the answer:

![](https://capella.pics/05b1b1c0-4482-45f0-b6eb-1f177764dece/resize/1300)

if there are `DEBUG=FALSE`, answer will be `Internal server error` and you can find error-message in the logs.

## Versioning

API [versioning](https://capella.pics/05b1b1c0-4482-45f0-b6eb-1f177764dece/resize/1300) provided by `@deprecated` directive.

## What to do next

- Requests batching http://webonyx.github.io/graphql-php/executing-queries/#query-batching
- Use [DataLoader](https://github.com/facebook/dataloader) or [GraphQL\Deferred](http://webonyx.github.io/graphql-php/data-fetching/#solving-n1-problem) for solving N+1 issue
- Validation
- Authentication scheme 
- Improve structure of Query and Mutation types using [Arguments, Aliases, Fragments, Operation name, Variables, Directives](http://graphql.org/learn/queries/)
- Move business logic from old `/versions/v1` classes to the new models.
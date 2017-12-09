## How to build up an image

`docker-compose up`

## How to look mongo collections

```bash
docker ps
```

Get a container id from `mongo:latest`

```bash
docker exec -ti <Container Id> mongo
```


```mongo
show dbs;
use <db>;
show collections;
```

## How to send request with CURL

In a new terminal tab

```bash
 curl -X POST --data 'password=qwdqq' http://localhost:8081/v1/user/create
```

If you see an error like

```
<br />
<b>Warning</b>:  require(../vendor/autoload.php): failed to open stream: No such file or directory in <b>/var/www/codex.notes.server/public/index.php</b> on line <b>14</b><br />
<br />
<b>Fatal error</b>:  require(): Failed opening required '../vendor/autoload.php' (include_path='.:/usr/local/lib/php') in <b>/var/www/codex.notes.server/public/index.php</b> on line <b>14</b><br />
```

u should update composer dependencies.


## How to enter a PHP process

```bash
docker ps
docker exec -ti <Container Id> /bin/bash
```

## How to enter a PHP process

```bash
docker ps
docker exec -ti <Container Id> /bin/bash
```

## How to enter a PHP process

```bash
docker ps
docker exec -ti <Container Id> /bin/bash
```






# Database

## Invites

1. Add email to the table

Who have an access to the folder:

`collaborators:<user_id>:<folder_id>`

`user_id` — folder owner, who send an invitation
`folder_id` — shared folder id

fields:
email | dt_add | auth_token

![](https://capella.pics/59ccf892-e5c6-4bfe-8b64-d50f2fac55c4)

2. Send email with `invitation token`

Invitation token contains: `<user_id>:<folder_id>:<hash (user_id+folder_id+salt) >`

3. User came from email with `invitation token`. On API public page with button 'Download an app'

4. User downloads an app and start it.

5. User clicks on `Accept an invitation` button with custom `codex://` protocol, that ca be handled by CodeX Notes.

6. An app sends request `/verifyCollaborator` to the API with user `invitation token` and `new user's id`

7. API `/verifyCollaborator`:

    - Get `user_id`, `folder_id` from the token, compare with `hash`.
    - Select collection `collaborators:<user_id>:<folder_id>`
    - Update email status to ACCEPTED, and save new users id;
    - send response with shared folder and notes


## Adding a new note in shared folder

1. Send `/sync` event with new item: it will be newer that `dt_sync`, where stored date of last syncronisation

2. API: in the `/sync` event we've got a new Note with `folder_id` and other note's data

4. Get all collaborators from `collaborators:<user_id>:<folder_id>`

5. Add (or update) a document in the `directories:<user_id>:<folder_id>` for all collaborators

    note (json) | dt_add | dt_modify






# Codex.Notes.Server

## User
### Create
`curl -X POST --data 'password=qwdqq' http://codex.notes.server/v1/user/create`

{"code":200,"success":true,"result":{"id":"713eaef0","password":{"hash":"c061abcfbb5eb3e92b7affd041c4161beb9c870da54702866d3c030e411d49e3","localSalt":"9b727826"}}}

### Get

`$ curl -X POST http://codex.notes.server/v1/user/get/713eaef0`

{"code":200,"success":true,"result":{"id":"713eaef0","password":{"hash":"ba20f4954e3326f344465c93b2fb8ecb5b13af9ec7a61e21dd9494eb4cbf1121","localSalt":"0ef53a6f"}}}

## Folder
### Create
`$ curl -X POST --data 'user=xhr3y0tm&id=00n1o111&name=example4&timestamp=111111111' http://codex.notes.server/v1/folder/create`

folder:00n1o110:xhr3y0tm
`{}`

folders:xhr3y0tm
`> db.getCollection('folders:xhr3y0tm').find({});
{ "_id" : ObjectId("5a01ea2661fa0f00073bee12"), "did" : "00n1o111", "title" : "example3", "timestamp" : 111111111 }`

{"code":200,"success":true,"result":true}
{"code":500,"success":false,"result":"Collection folder:00n1o103:xhr3y0tm is not created: a collection 'notes.folder:00n1o103:xhr3y0tm' already exists"}

### Delete

`curl -X POST --data 'user=xhr3y0tm&id=00n1o111' http://codex.notes.server/v1/folder/delete`

{"code":200,"success":true,"result":true}

## Errors
`$ curl -X POST --data 'pass=123' http://codex.notes.server/v1/user/create?pas`

{"code":500,"success":false,"result":"Internal server error"}

`$ curl -X POST http://codex.notes.server/v1/user/`

{"code":404,"success":false,"result":"Route \/v1\/user\/ not found"}

`$ curl -X POST http://codex.notes.server/v1/user/get/713eaef04duw`

{"code":400,"success":false,"result":"User id length is not 8"}

`$ curl -X POST --data 'password=' http://codex.notes.server/v1/user/create`

{"code":400,"success":false,"result":"Password is empty"}

`$ curl -X GET --data 'password=123' http://codex.notes.server/v1/user/create`

{"code":405,"success":false,"result":"Method must be one of: POST"}

{"code":500,"success":false,"result":"Collection folder:00n1o103:xhr3y0tm is not created: a collection 'notes.folder:00n1o103:xhr3y0tm' already exists"}

## Install composer dependencies

$ docker exec notesserver_php_1 composer install
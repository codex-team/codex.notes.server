# Codex.Notes.Server

## Создать пользователя
`curl -X POST --data 'password=qwdqq' http://codex.notes.server/v1/user/create`

{"code":200,"success":true,"result":{"id":"713eaef0","password":{"hash":"c061abcfbb5eb3e92b7affd041c4161beb9c870da54702866d3c030e411d49e3","localSalt":"9b727826"}}}

## Получить данные

`$ curl -X POST http://codex.notes.server/v1/user/get/713eaef0`

{"code":200,"success":true,"result":{"id":"713eaef0","password":{"hash":"ba20f4954e3326f344465c93b2fb8ecb5b13af9ec7a61e21dd9494eb4cbf1121","localSalt":"0ef53a6f"}}}

## Install composer dependencies

$ docker exec notesserver_php_1 composer install

## Ошибка запроса
`$ curl -X POST --data 'pass=123' http://codex.notes.server/v1/user/create?pas`

{"code":500,"success":false,"result":"Internal server error"}

`$ curl -X POST http://codex.notes.server/v1/user/`

{"code":404,"success":false,"result":"Route \/v1\/user\/ not found"}

`$ curl -X POST http://codex.notes.server/v1/user/get/713eaef04duw`

{"code":400,"success":false,"result":"User id length is not 8"}

`$ curl -X POST --data 'password=' http://codex.notes.server/v1/user/create`

{"code":400,"success":false,"result":"Password is empty"}

`$ curl -X GET --data 'password=123' http://codex.notes.server:8081/v1/user/create`

{"code":405,"success":false,"result":"Method must be one of: POST"}


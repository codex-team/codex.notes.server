# Codex.Notes.Server

## Создать пользователя
curl -X POST --data 'password=qwdqq' http://codex.notes.server/user/create

{"success":true,"result":{"uid":"89c2633b"}}

## Получить данные

$ curl -X POST http://codex.notes.server/user/get/89c2633b

{"success":true,"result":{"_id":{"$oid":"59de92565d945f103933fd82"},"user_id":"89c2633b","password":{"hash":"5195c9ef71d1d26ffc0ff2832f9bf286eb574e8d7826ebd9f94f3d1ad19ad68f","localSalt":"1090dda9"},"ip":"::1","directories":[]}}
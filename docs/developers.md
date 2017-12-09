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



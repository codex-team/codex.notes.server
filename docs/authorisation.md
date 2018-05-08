# Authorisation

...

## Desktop auth
 
...

## Mobile auth

...

## JWT and payload data.

Success auth response will contain the json encoded array with the following data 
   
- `name` — user's name from Google
- `photo` — path to user's photo
- `dtModify` — timestamp of last model modifying
- `channel` — user's sockets channel for updates
- `jwt` — JWT used for authentication

Data in JWT

- `iss` — token's issuer "CodeX Notes API Server"
- `aud` — token's purpose "CodeX Notes Application"
- `iat` — issued at timestamp
- `user_id` — user's Id in CodeX Notes DB
- `googleId` — Google Id
- `email` — user's email from Google
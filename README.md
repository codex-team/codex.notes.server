# API

## Create new user
```graphql
mutation CreateUser {
  user(
    id:"5a70ac62e1d8ff5cda8322a0",
    name:"Svyatoslav Fedorovich",
    email:"slavakpss@yandex.ru"
  ){
    name,
    folders {
      title
    }
  }
}
```
### Parameters description

| Parameter | Type | Description |
| -- | -- | -- |
| ID | String | User's unique identifier. 24-character hexadecimal string |
| name | String | User's nickname |
| email | String | User's email address |

## Create new folder
```graphql
mutation CreateFolder{
  folder(
    id: "5a70ac62e1d8ff5cda8322a8",
    title: "Noo",
    ownerId: "5a70ac62e1d8ff5cda8322a0",
  ){
    title,
    id,
    owner{
      id,
      name
    }
  }
}
```
### Parameters description

| Parameter | Type | Description |
| -- | -- | -- |
| ID | String | Folder's unique identifier. 24-character hexadecimal string |
| title | String | Folder's name |
| ownerId | String | User's id |

## Create new note
```graphql
mutation CreateNote {
  note(
    id: "5a70ac62e1d8ff5cda8322a2",
    authorId: "5a70ac62e1d8ff5cda8322a0", 
    folderId: "5a70ac62e1d8ff5cda8322a4", //Folder must exists at the DB
    title: "How to work with API",
    content: "{}"
  ) {
    id,
    title,
    content,
    dtCreate,
    dtModify,
    author {
      id,
      name,
      email,
      dtReg
    }
    
  }
}
```
### Parameters description

| Parameter | Type | Description |
| -- | -- | -- |
| ID | String | Note's unique identifier |
| title | String | Note's public title |
| content | String | Note's content in the JSON-format |
| folderId | String | Note's folder |
| authorId | String | Note's author |

## Get user info
```graphql
query Sync {
  user(
    id: "5a70ac62e1d8ff5cda8322a0"
  ){
    name,
    folders {
      id, 
      title,
      owner {
        name,
        id
      },
      notes {
        id,
        title,
        content,
        dtCreate,
        dtModify,
        author {
          id,
          name,
          email
        },
        isRemoved
      }
    }
  }
}
```


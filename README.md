# API

## Create new user
```
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
```
id: ID
User's unique identifier. 24-character hexadecimal string

name: String
User's nickname

email: String
User's email address
```

## Create new folder
```
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
```
```

## Create new note
```
mutation CreateNote {
  note(
    id: "5a70ac62e1d8ff5cda8322a2",
    authorId: "5a70ac62e1d8ff5cda8322a0", 
    folderId: "5a70ac62e1d8ff5cda8322a4", 
    title: "How to work with f*cking API",
    content: "In no way"
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
```
id: ID
Note's unique identifier

title: String
Note's public title

content: String
Note's content in the JSON-format

dtCreate: Int
Note's creation timestamp

dtModify: Int
Note's last modification timestamp

author: User
Note's author

isRemoved: Boolean
Removed status: true if Note marked as removed
```

## Create new note
```
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
### Parameters description
```
```


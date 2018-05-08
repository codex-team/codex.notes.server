# User's sockets channel

We use sockets to send auth jwt and notify user about Folders and Notes updates he contribute to.

## Updates

Each update will contain json encoded array.

- `event` — type of update
- `data` — payload info
- `sender` — sender User's model

### Events

- `folder updated`
- `note updated`
- `collaborator invited`
- `collaborator joined`

### Data

#### Note

- `id` string
- `title` string
- `content` string
- `dtCreate` number
- `dtModify` number
- `isRemoved` boolean
- `folder` Folder

#### Folder

- `id` string
- `title` string
- `dtCreate` number
- `dtModify` number
- `isShared` boolean
- `isRemoved` boolean
- `isRoot` boolean
- `ownerId` string

#### Collaborator

- `id` string
- `token` string
- `email` string
- `dtInvite` number
- `isRemoved` boolean
- `user` User
- `folderId` string

### Sender

User's model

- `id` string
- `name` string
- `email` string
- `photo` string
- `googleId` string
- `dtReg` number
- `dtModify` number
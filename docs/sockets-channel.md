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

- `id`
- `title`
- `content`
- `dtCreate`
- `dtModify`
- `isRemoved`
- `folder`

#### Folder

- `id`
- `title`
- `dtCreate`
- `dtModify`
- `isShared`
- `isRemoved`
- `isRoot`
- `ownerId`

#### Collaborator

- `id`
- `token`
- `email`
- `dtInvite`
- `isRemoved`
- `user`
- `folderId`

### Sender

User's model

- `id`
- `name`
- `email`
- `photo` 
- `googleId`
- `dtReg`
- `dtModify`
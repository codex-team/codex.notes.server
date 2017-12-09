# Database

![](https://capella.pics/5be4ad62-0b30-4348-aa0d-838279bf46cd)

## Collections

| Name | Description |
| -- | -- |
| `Users` | All users  |
| `directories:<owner_id>`  |  List of user's folders |
| `collaborators:<owner_id>:<folder_id>` | list of collaborators of specified folder |
| `directory:<owner_id>:<folder_id>` | List of notes in directory |

All `ids` is a mongo_id from specified collection:

- `<owner_id>` — `_id` from `Users`
- `<folder_id>` — `_id` from `directories:<owner_id>`

## Folder creation

### 1. Add a document in the `directories:<owner_id>`

#### Fields
| Param | Type | Description |
|--|--|--|
| `_id` | mongoId | unique mongo id |
| `is_shared` | boolean | `false` on creation, `true` on sharing |
| `sharer_id` | mongoId | Person who created a directory |
| `title` | string | Folder title |
| ... | ... | other directory data |


where `is_shared` — 0
where `sharer_id` — current user

### 2. Create `directory:<owner_id>:<folder_id>` for the current user

## Invite a collaborator

![](https://capella.pics/59ccf892-e5c6-4bfe-8b64-d50f2fac55c4)

### 1. Add an email to the `collaborators:<owner_id>:<folder_id>`

`owner_id` — folder owner, who send an invitation
`folder_id` — shared folder id

#### Fields

| Param | Type | Description |
|--|--|--|
| `_id` | mongoId | unique mongo id |
| `email` | string | Invitation acceptor's email |
| `collaborator_id` | mongoId \| null (default) | Invitation acceptor's `_id` from `Users`. |
| `invitation_token` | string | Token with `<owner_id>:<folder_id>:<hash>`  |
| `dt_add` | mongoId | Date of an invitation |

### 2. Send email with `invitation token`

Invitation token contains:

**`<owner_id>:<folder_id>:<hash>`**

- `owner_id` — Who sent an invitation
- `folder_id` — On which folder
- `hash` — Security hash with (`owner_id`+`folder_id`+`salt`)

### 3. User came from email with the `invitation token`. On the API public page with button `Download an app`

### 4. User downloads an app and start it.

### 5. User clicks on `Accept an invitation` button with custom `codex://` protocol, that can be handled by CodeX Notes.

### 6. An app sends request `/verifyCollaborator` to the API with the user's `invitation token` and new user's `_id`

### 7. API `/verifyCollaborator`:

- Get `owner_id` and `folder_id` from the token, validate with `hash`.
- Select collection `collaborators:<owner_id>:<folder_id>`
- Update an email status to `accepted`, and save a new user's `_id`;
- Add a new folder to the `directories:<acceptor_id>` (invited user) with `owner_id` (current user) and `is_shared` = 1
- Update an item in the `directories:<owner_id>` (current user) with `is_shared` = 1
- send a response with the shared folder data and notes list


## Adding a new note in a shared folder

1. Send `/sync` event with new item: it will be newer that `dt_sync`, where stored date of last syncronisation
2. API: in the `/sync` event we've got a new Note with `folder_id` and other note's data
4. Get all collaborators from `collaborators:<owner_id>:<folder_id>`
5. Add (or update) a document in the `directory:<owner_id>:<folder_id>`

#### Fields
| Param | Type | Description |
|--|--|--|
| `_id` | mongoId | `Note` unique mongo id |
| `note` | json | `Note` data |
| `dt_add` | timestamp | `Note` creation date |
| `dt_modify` | timestamp | `Note` last updating date |


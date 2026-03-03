# API Documentation

## API Overview

**Base URL:** `/api/index.php`  
**Authentication:** API Key (POST parameter: `key`)  
**Content-Type:** `application/x-www-form-urlencoded` or `multipart/form-data`

---

## Authentication

### API Key Authentication

```http
POST /api/index.php
Content-Type: application/x-www-form-urlencoded

key=YOUR_API_KEY&method=get&resource=clients
```

**API Key Management:**
- API keys stored in `api_keys` table
- Each key linked to a role (role-based permissions)
- Permissions checked via `isAuthorizedApi()` function

**Response Codes:**
- `902` - API key missing
- `903` - Authentication failed (invalid key)
- `905` - Not authorized to perform action

---

## Request Format

### Common Parameters

| Parameter | Required | Description |
|-----------|----------|-------------|
| `key` | Yes | API secret key |
| `method` | Yes | HTTP method: `get`, `add`, `edit`, `delete`, `attach`, `detach` |
| `resource` | Yes | Resource name (see list below) |
| `data` | Conditional | Array of data (for add/edit/attach/detach) |
| `filters` | Conditional | Array of filters (for get) |
| `id` | Conditional | Resource ID (for delete) |

---

## HTTP Methods

### GET - Retrieve Resources

```http
POST /api/index.php
key=YOUR_API_KEY
method=get
resource=clients
filters[name]=Acme Corp
```

**Response:**
```json
{
  "status": 200,
  "data": [
    {
      "id": 1,
      "name": "Acme Corp",
      "asset_tag_prefix": "AC",
      "license_tag_prefix": "LC"
    }
  ]
}
```

---

### ADD - Create Resource

```http
POST /api/index.php
key=YOUR_API_KEY
method=add
resource=clients
data[name]=New Client
data[asset_tag_prefix]=NC
data[license_tag_prefix]=NL
```

**Response:**
```json
{
  "status": 10,
  "status_message": "Record added successfully"
}
```

---

### EDIT - Update Resource

```http
POST /api/index.php
key=YOUR_API_KEY
method=edit
resource=clients
data[id]=1
data[name]=Updated Name
```

**Response:**
```json
{
  "status": 20,
  "status_message": "Record updated successfully"
}
```

---

### DELETE - Remove Resource

```http
POST /api/index.php
key=YOUR_API_KEY
method=delete
resource=clients
id=1
```

**Response:**
```json
{
  "status": 30,
  "status_message": "Record deleted successfully"
}
```

---

### ATTACH - Create Relationship

```http
POST /api/index.php
key=YOUR_API_KEY
method=attach
resource=licenses
data[licenseid]=5
data[assetid]=10
```

---

### DETACH - Remove Relationship

```http
POST /api/index.php
key=YOUR_API_KEY
method=detach
resource=licenses
data[id]=15
```

---

## Available Resources

### 1. clients
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `name` (string, required)
- `asset_tag_prefix` (string)
- `license_tag_prefix` (string)
- `notes` (text)

**Permissions:**
- GET: `viewClient`
- ADD: `addClient`
- EDIT: `editClient`
- DELETE: `deleteClient`

---

### 2. assets
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `categoryid` (int, required)
- `adminid` (int)
- `clientid` (int, required)
- `userid` (int)
- `manufacturerid` (int)
- `modelid` (int)
- `supplierid` (int)
- `statusid` (int)
- `locationid` (int)
- `purchase_date` (date: YYYY-MM-DD)
- `warranty_months` (int)
- `tag` (string)
- `name` (string, required)
- `serial` (string)
- `notes` (text)
- `qrvalue` (string)
- Custom fields (dynamic)

**Permissions:**
- GET: `viewAsset`
- ADD: `addAsset`
- EDIT: `editAsset`
- DELETE: `deleteAsset`

---

### 3. licenses
**Methods:** GET, ADD, EDIT, DELETE, ATTACH, DETACH

**Fields:**
- `clientid` (int, required)
- `statusid` (int)
- `categoryid` (int)
- `supplierid` (int)
- `seats` (int)
- `tag` (string)
- `name` (string, required)
- `serial` (string, encrypted)
- `notes` (text)
- `qrvalue` (string)
- Custom fields (dynamic)

**ATTACH/DETACH:**
- Attach license to asset
- `data[licenseid]` and `data[assetid]` for ATTACH
- `data[id]` (licenses_assets.id) for DETACH

**Permissions:**
- GET: `viewLicense`
- ADD: `addLicense`
- EDIT: `editLicense`
- DELETE: `deleteLicense`
- ATTACH/DETACH: `assetLicense`

---

### 4. credentials
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `clientid` (int, required)
- `name` (string, required)
- `username` (string)
- `password` (string, encrypted)
- `notes` (text)

**Permissions:**
- GET: `viewCredential`
- ADD: `addCredential`
- EDIT: `editCredential`
- DELETE: `deleteCredential`

---

### 5. asset_categories
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `name` (string, required)
- `color` (string, hex color)

**Permissions:** `manageData`

---

### 6. license_categories
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `name` (string, required)
- `color` (string, hex color)

**Permissions:** `manageData`

---

### 7. status_labels
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `name` (string, required)
- `color` (string, hex color)
- `type` (string: 'asset' or 'license')

**Permissions:** `manageData`

---

### 8. manufacturers
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `name` (string, required)

**Permissions:** `manageData`

---

### 9. models
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `name` (string, required)

**Permissions:** `manageData`

---

### 10. locations
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `clientid` (int, required)
- `name` (string, required)

**Permissions:** `manageData`

---

### 11. suppliers
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `name` (string, required)

**Permissions:** `manageData`

---

### 12. projects
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `clientid` (int, required)
- `tag` (string)
- `name` (string, required)
- `notes` (text)
- `description` (text)
- `startdate` (date: YYYY-MM-DD)
- `deadline` (date: YYYY-MM-DD)
- `progress` (int: -1 for auto, 0-100 for manual)

**Permissions:**
- GET: `viewProject`
- ADD: `addProject`
- EDIT: `editProject`
- DELETE: `deleteProject`

---

### 13. tickets
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `departmentid` (int)
- `clientid` (int)
- `userid` (int)
- `adminid` (int)
- `assetid` (int)
- `projectid` (int)
- `email` (string, required)
- `subject` (string, required)
- `message` (text, required for ADD)
- `status` (string: Open, In Progress, Answered, Reopened, Closed)
- `priority` (string: Low, Normal, High)
- `notes` (text)
- `ccs` (array of email addresses)

**Permissions:**
- GET: `viewTicket`
- ADD: `addTicket`
- EDIT: `editTicket`
- DELETE: `deleteTicket`

---

### 14. ticket_replies
**Methods:** GET, ADD

**Fields:**
- `ticketid` (int, required)
- `peopleid` (int, required)
- `message` (text, required)

**Permissions:**
- GET: `viewTicket`
- ADD: `manageTicket`

---

### 15. issues
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `projectid` (int, required)
- `milestoneid` (int)
- `adminid` (int)
- `clientid` (int)
- `tag` (string)
- `name` (string, required)
- `description` (text)
- `status` (string: To Do, In Progress, Done)
- `priority` (string: Low, Normal, High)

**Permissions:**
- GET: `viewIssue`
- ADD: `addIssue`
- EDIT: `editIssue`
- DELETE: `deleteIssue`

---

### 16. kb_categories
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `name` (string, required)
- `description` (text)

**Permissions:**
- GET: `viewKB`
- ADD/EDIT/DELETE: `addKB`

---

### 17. kb_articles
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `categoryid` (int, required)
- `title` (string, required)
- `content` (text, required)

**Permissions:**
- GET: `viewKB`
- ADD/EDIT/DELETE: `addKB`

---

### 18. monitoring_hosts
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `clientid` (int, required)
- `name` (string, required)
- `ip` (string, required)
- `notes` (text)

**Permissions:**
- GET: `viewHost`
- ADD: `addHost`
- EDIT: `editHost`
- DELETE: `deleteHost`

---

### 19. monitoring_checks
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `hostid` (int, required)
- `type` (string: ping, http, https, port)
- `port` (int)
- `keyword` (string)

**Permissions:**
- GET: `viewHost`
- ADD/EDIT/DELETE: `manageHost`

---

### 20. users
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `name` (string, required)
- `roleid` (int, required)
- `clientid` (int, required)
- `email` (string, required, unique)
- `ldap_user` (string)
- `title` (string)
- `mobile` (string)
- `password` (string, required for ADD)
- `theme` (string)
- `sidebar` (string)
- `layout` (string)
- `notes` (text)
- `lang` (string)

**Permissions:**
- GET: `viewUser`
- ADD: `addUser`
- EDIT: `editUser`
- DELETE: `deleteUser`

---

### 21. staff
**Methods:** GET, ADD, EDIT, DELETE

**Fields:** Same as users, but type='admin'

**Permissions:**
- GET: `viewStaff`
- ADD: `addStaff`
- EDIT: `editStaff`
- DELETE: `deleteStaff`

---

### 22. roles
**Methods:** GET

**Fields:**
- `name` (string)
- `perms` (serialized array)

**Permissions:** `viewRole`

---

### 23. languages
**Methods:** GET

**Fields:**
- Language code and name

**Permissions:** None (public)

---

### 24. contacts
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `clientid` (int, required)
- `name` (string, required)
- `email` (string)
- `mobile` (string)
- `notes` (text)

**Permissions:**
- GET: `viewContact`
- ADD: `addContact`
- EDIT: `editContact`
- DELETE: `deleteContact`

---

### 25. comments
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `issueid` (int, required)
- `peopleid` (int, required)
- `comment` (text, required)

**Permissions:**
- GET: `viewComment`
- ADD: `addComment`
- EDIT: `editComment`
- DELETE: `deleteComment`

---

### 26. milestones
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `projectid` (int, required)
- `name` (string, required)
- `description` (text)
- `releasedate` (date: YYYY-MM-DD)
- `released` (boolean)

**Permissions:**
- GET: `viewMilestone`
- ADD: `addMilestone`
- EDIT: `editMilestone`
- DELETE: `deleteMilestone`

---

### 27. predefined_replies
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `name` (string, required)
- `reply` (text, required)

**Permissions:**
- GET: `viewPReply`
- ADD: `addPReply`
- EDIT: `editPReply`
- DELETE: `deletePReply`

---

### 28. custom_asset_fields
**Methods:** GET

**Fields:**
- `name` (string)
- `type` (string)

**Permissions:** `viewCustomField`

---

### 29. custom_license_fields
**Methods:** GET

**Fields:**
- `name` (string)
- `type` (string)

**Permissions:** `viewCustomField`

---

### 30. ticket_departments
**Methods:** GET

**Fields:**
- `name` (string)
- `email` (string)

**Permissions:** `viewTicket`

---

### 31. config
**Methods:** GET

**Fields:**
- `name` (string)
- `value` (string)

**Permissions:** `manageSettings`

---

### 32. time_log
**Methods:** GET, ADD, EDIT, DELETE

**Fields:**
- `projectid` (int)
- `issueid` (int)
- `clientid` (int)
- `peopleid` (int, required)
- `date` (date: YYYY-MM-DD, required)
- `start` (time: HH:MM, required)
- `end` (time: HH:MM, required)
- `description` (text)

**Permissions:**
- GET: `viewTime`
- ADD: `addTime`
- EDIT: `editTime`
- DELETE: `deleteTime`

---

### 33. system_log
**Methods:** GET

**Fields:**
- `peopleid` (int)
- `ipaddress` (string)
- `description` (text)
- `timestamp` (datetime)

**Permissions:** `viewSystemLog`

---

### 34. files
**Methods:** GET, ADD, DELETE

**Fields:**
- `clientid` (int)
- `projectid` (int)
- `assetid` (int)
- `ticketreplyid` (int)
- `name` (string)
- `file` (file upload)

**Permissions:**
- GET: `viewFile`
- ADD: `uploadFile`
- DELETE: `deleteFile`

---

### 35. qrcodes
**Methods:** GET, ATTACH, DETACH

**Fields:**
- `batchid` (int)
- `value` (string)
- `type` (string: asset or license)
- `attached` (boolean)

**ATTACH:**
- `data[qrcodeid]` and `data[assetid]` or `data[licenseid]`

**DETACH:**
- `data[id]` (asset or license id)

**Permissions:** `manageData`

---

### 36. authenticate
**Method:** POST (special endpoint)

**Purpose:** Authenticate user and get user ID

**Fields:**
- `email` (string, required)
- `password` (string, required)

**Response:**
```json
{
  "status": 200,
  "userid": 5
}
```

**No permissions required** (public endpoint)

---

## Response Status Codes

### Success Codes
- `10` - Record added successfully
- `20` - Record updated successfully
- `30` - Record deleted successfully
- `40` - Settings updated successfully
- `200` - Success (general)

### Error Codes
- `11` - Failed to add record
- `901` - Unknown error
- `902` - API key missing
- `903` - Authentication failed (invalid API key)
- `904` - Resource does not exist
- `905` - Not authorized to perform action
- `906` - Request method not found
- `908` - 'filters' parameter error (expected array)
- `909` - 'data' parameter error (expected array)
- `910` - 'data' array missing
- `911` - 'id' parameter error (expected string)
- `912` - 'id' string missing

### File Upload Codes
- `9500` - File uploaded successfully
- `9501` - File already exists
- `9502` - Failed to move uploaded file
- `9503` - File deleted successfully
- `9504` - Failed to delete file

---

## Example API Calls

### Get All Clients
```bash
curl -X POST https://your-domain.com/api/index.php \
  -d "key=YOUR_API_KEY" \
  -d "method=get" \
  -d "resource=clients"
```

### Add New Asset
```bash
curl -X POST https://your-domain.com/api/index.php \
  -d "key=YOUR_API_KEY" \
  -d "method=add" \
  -d "resource=assets" \
  -d "data[name]=Dell Laptop" \
  -d "data[clientid]=1" \
  -d "data[categoryid]=2" \
  -d "data[tag]=ASSET-001"
```

### Get Tickets with Filter
```bash
curl -X POST https://your-domain.com/api/index.php \
  -d "key=YOUR_API_KEY" \
  -d "method=get" \
  -d "resource=tickets" \
  -d "filters[status]=Open" \
  -d "filters[priority]=High"
```

### Assign License to Asset
```bash
curl -X POST https://your-domain.com/api/index.php \
  -d "key=YOUR_API_KEY" \
  -d "method=attach" \
  -d "resource=licenses" \
  -d "data[licenseid]=5" \
  -d "data[assetid]=10"
```

### Authenticate User
```bash
curl -X POST https://your-domain.com/api/index.php \
  -d "key=YOUR_API_KEY" \
  -d "method=post" \
  -d "resource=authenticate" \
  -d "data[email]=user@example.com" \
  -d "data[password]=password123"
```

---

## Migration Notes for Laravel

### Recommended Approach
1. **Keep API backward compatible** during migration
2. **Create API versioning** (v1 = PHP, v2 = Laravel)
3. **Use Laravel API Resources** for response formatting
4. **Implement same permission system** initially
5. **Gradually migrate to Laravel Sanctum** for authentication

### Laravel API Structure
```
routes/api.php
├─ /api/v1/* (backward compatible with PHP API)
└─ /api/v2/* (new Laravel API with improvements)

app/Http/Controllers/Api/
├─ V1/ (legacy compatibility)
└─ V2/ (new implementation)
```

### Improvements for Laravel API
1. **JWT or Sanctum** instead of API keys
2. **RESTful routes** instead of method parameter
3. **JSON responses** with proper HTTP status codes
4. **Rate limiting**
5. **API documentation** (Swagger/OpenAPI)
6. **Validation** with Form Requests
7. **Pagination** for large datasets

---

**Document Version:** 1.0  
**Last Updated:** 2025-11-22

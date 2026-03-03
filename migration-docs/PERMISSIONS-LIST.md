# Complete Permissions List

## All System Permissions

This document lists all permissions used in the system for role-based access control.

---

## Permission Categories

### 1. Asset Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewAsset` | View assets | Asset list, details |
| `addAsset` | Create new asset | Add asset form |
| `editAsset` | Edit existing asset | Edit asset form |
| `deleteAsset` | Delete asset | Delete action |
| `assetLicense` | Assign/unassign licenses to assets | License assignment |

---

### 2. License Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewLicense` | View licenses | License list, details |
| `addLicense` | Create new license | Add license form |
| `editLicense` | Edit existing license | Edit license form |
| `deleteLicense` | Delete license | Delete action |

---

### 3. Client Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewClient` | View clients | Client list, details |
| `addClient` | Create new client | Add client form |
| `editClient` | Edit existing client | Edit client form, save notes |
| `deleteClient` | Delete client | Delete action |
| `adminsClient` | Assign/unassign staff to clients | Staff assignment |

---

### 4. User Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewUser` | View users | User list, details |
| `addUser` | Create new user | Add user form |
| `editUser` | Edit existing user | Edit user form |
| `deleteUser` | Delete user | Delete action |

---

### 5. Staff Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewStaff` | View staff members | Staff list, details |
| `addStaff` | Create new staff | Add staff form |
| `editStaff` | Edit existing staff | Edit staff form |
| `deleteStaff` | Delete staff | Delete action |

---

### 6. Ticket Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewTicket` | View tickets | Ticket list, details |
| `addTicket` | Create new ticket | Add ticket form |
| `editTicket` | Edit existing ticket | Edit ticket form |
| `deleteTicket` | Delete ticket | Delete action |
| `manageTicket` | Manage ticket (add replies) | Add reply, change status |
| `manageTicketNotes` | Edit ticket notes | Internal notes |

---

### 7. Project Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewProject` | View projects | Project list, details |
| `addProject` | Create new project | Add project form |
| `editProject` | Edit existing project | Edit project form |
| `deleteProject` | Delete project | Delete action |
| `adminsProject` | Assign/unassign staff to projects | Staff assignment |
| `manageProjectNotes` | Edit project notes | Internal notes |

---

### 8. Issue Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewIssue` | View issues | Issue list, details |
| `addIssue` | Create new issue | Add issue form |
| `editIssue` | Edit existing issue | Edit issue form |
| `deleteIssue` | Delete issue | Delete action |

---

### 9. Milestone Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewMilestone` | View milestones | Milestone list, details |
| `addMilestone` | Create new milestone | Add milestone form |
| `editMilestone` | Edit existing milestone | Edit milestone form |
| `deleteMilestone` | Delete milestone | Delete action |
| `releaseMilestone` | Release milestone | Release action |

---

### 10. Comment Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewComment` | View comments | Comment list |
| `addComment` | Add new comment | Add comment form |
| `editComment` | Edit existing comment | Edit comment form |
| `deleteComment` | Delete comment | Delete action |

---

### 11. File Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewFile` | View files | File list, download |
| `uploadFile` | Upload files | File upload |
| `deleteFile` | Delete files | Delete action |

---

### 12. Credential Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewCredential` | View credentials | Credential list, details |
| `addCredential` | Create new credential | Add credential form |
| `editCredential` | Edit existing credential | Edit credential form |
| `deleteCredential` | Delete credential | Delete action |

---

### 13. Contact Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewContact` | View contacts | Contact list, details |
| `addContact` | Create new contact | Add contact form |
| `editContact` | Edit existing contact | Edit contact form |
| `deleteContact` | Delete contact | Delete action |

---

### 14. Monitoring

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewHost` | View monitoring hosts | Host list, details |
| `addHost` | Create new host | Add host form |
| `editHost` | Edit existing host | Edit host form |
| `deleteHost` | Delete host | Delete action |
| `manageHost` | Manage host (checks, alerts) | Add/edit checks, assign people |

---

### 15. Knowledge Base

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewKB` | View KB articles | KB list, article view |
| `addKB` | Create/edit KB content | Add/edit articles, categories |
| `editKB` | Edit KB content | Edit articles, categories |
| `deleteKB` | Delete KB content | Delete articles, categories |

---

### 16. Time Logging

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewTime` | View time logs | Time log list |
| `addTime` | Add time log | Add time log form |
| `editTime` | Edit time log | Edit time log form |
| `deleteTime` | Delete time log | Delete action |

---

### 17. Predefined Replies

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewPReply` | View predefined replies | Reply list |
| `addPReply` | Create new reply | Add reply form |
| `editPReply` | Edit existing reply | Edit reply form |
| `deletePReply` | Delete reply | Delete action |

---

### 18. Role Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewRole` | View roles | Role list, details |
| `addRole` | Create new role | Add role form |
| `editRole` | Edit existing role | Edit role form |
| `deleteRole` | Delete role | Delete action |

---

### 19. Data Management (Attributes)

| Permission | Description | Used In |
|------------|-------------|---------|
| `manageData` | Manage all attribute data | Categories, manufacturers, models, suppliers, locations, status labels, QR codes |

**Includes:**
- Asset categories (add, edit, delete)
- License categories (add, edit, delete)
- Status labels (add, edit, delete)
- Manufacturers (add, edit, delete)
- Models (add, edit, delete)
- Suppliers (add, edit, delete)
- Locations (add, edit, delete)
- QR codes (generate, edit, delete, attach, detach)

---

### 20. Custom Fields

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewCustomField` | View custom fields | Custom field list |
| `addCustomField` | Create new custom field | Add custom field form |
| `editCustomField` | Edit existing custom field | Edit custom field form |
| `deleteCustomField` | Delete custom field | Delete action |

---

### 21. API Management

| Permission | Description | Used In |
|------------|-------------|---------|
| `manageApiKeys` | Manage API keys | Add, edit, delete API keys |

---

### 22. System Settings

| Permission | Description | Used In |
|------------|-------------|---------|
| `manageSettings` | Manage system settings | All settings pages, notification templates, departments, languages |

**Includes:**
- General settings
- Localization settings
- Label settings
- Email settings
- SMS settings
- LDAP settings
- Ticket settings
- Notification templates
- Support departments
- Languages

---

### 23. System Logs

| Permission | Description | Used In |
|------------|-------------|---------|
| `viewSystemLog` | View system logs | System log page |
| `viewEmailLog` | View email logs | Email log page |
| `viewSMSLog` | View SMS logs | SMS log page |

---

## Permission Groups (Suggested Roles)

### Super Admin
**All permissions** - Full system access

```php
[
    'viewAsset', 'addAsset', 'editAsset', 'deleteAsset', 'assetLicense',
    'viewLicense', 'addLicense', 'editLicense', 'deleteLicense',
    'viewClient', 'addClient', 'editClient', 'deleteClient', 'adminsClient',
    'viewUser', 'addUser', 'editUser', 'deleteUser',
    'viewStaff', 'addStaff', 'editStaff', 'deleteStaff',
    'viewTicket', 'addTicket', 'editTicket', 'deleteTicket', 'manageTicket', 'manageTicketNotes',
    'viewProject', 'addProject', 'editProject', 'deleteProject', 'adminsProject', 'manageProjectNotes',
    'viewIssue', 'addIssue', 'editIssue', 'deleteIssue',
    'viewMilestone', 'addMilestone', 'editMilestone', 'deleteMilestone', 'releaseMilestone',
    'viewComment', 'addComment', 'editComment', 'deleteComment',
    'viewFile', 'uploadFile', 'deleteFile',
    'viewCredential', 'addCredential', 'editCredential', 'deleteCredential',
    'viewContact', 'addContact', 'editContact', 'deleteContact',
    'viewHost', 'addHost', 'editHost', 'deleteHost', 'manageHost',
    'viewKB', 'addKB', 'editKB', 'deleteKB',
    'viewTime', 'addTime', 'editTime', 'deleteTime',
    'viewPReply', 'addPReply', 'editPReply', 'deletePReply',
    'viewRole', 'addRole', 'editRole', 'deleteRole',
    'manageData',
    'viewCustomField', 'addCustomField', 'editCustomField', 'deleteCustomField',
    'manageApiKeys',
    'manageSettings',
    'viewSystemLog', 'viewEmailLog', 'viewSMSLog',
]
```

---

### IT Manager
**Manage assets, licenses, tickets, projects**

```php
[
    'viewAsset', 'addAsset', 'editAsset', 'deleteAsset', 'assetLicense',
    'viewLicense', 'addLicense', 'editLicense', 'deleteLicense',
    'viewClient', 'editClient',
    'viewUser', 'addUser', 'editUser',
    'viewStaff',
    'viewTicket', 'addTicket', 'editTicket', 'manageTicket', 'manageTicketNotes',
    'viewProject', 'addProject', 'editProject', 'adminsProject', 'manageProjectNotes',
    'viewIssue', 'addIssue', 'editIssue',
    'viewMilestone', 'addMilestone', 'editMilestone', 'releaseMilestone',
    'viewComment', 'addComment', 'editComment',
    'viewFile', 'uploadFile',
    'viewCredential', 'addCredential', 'editCredential',
    'viewContact', 'addContact', 'editContact',
    'viewHost', 'addHost', 'editHost', 'manageHost',
    'viewKB', 'addKB', 'editKB',
    'viewTime', 'addTime', 'editTime',
    'viewPReply', 'addPReply', 'editPReply',
    'manageData',
]
```

---

### Support Agent
**Manage tickets, view assets**

```php
[
    'viewAsset',
    'viewLicense',
    'viewClient',
    'viewUser',
    'viewTicket', 'addTicket', 'editTicket', 'manageTicket',
    'viewProject',
    'viewIssue',
    'viewComment', 'addComment',
    'viewFile', 'uploadFile',
    'viewContact',
    'viewKB',
    'viewPReply',
]
```

---

### Asset Manager
**Manage assets and licenses only**

```php
[
    'viewAsset', 'addAsset', 'editAsset', 'deleteAsset', 'assetLicense',
    'viewLicense', 'addLicense', 'editLicense', 'deleteLicense',
    'viewClient',
    'viewUser',
    'viewFile', 'uploadFile',
    'manageData', // For categories, manufacturers, etc.
]
```

---

### Project Manager
**Manage projects and issues**

```php
[
    'viewClient',
    'viewUser',
    'viewStaff',
    'viewProject', 'addProject', 'editProject', 'adminsProject', 'manageProjectNotes',
    'viewIssue', 'addIssue', 'editIssue', 'deleteIssue',
    'viewMilestone', 'addMilestone', 'editMilestone', 'deleteMilestone', 'releaseMilestone',
    'viewComment', 'addComment', 'editComment', 'deleteComment',
    'viewFile', 'uploadFile',
    'viewTime', 'addTime', 'editTime',
]
```

---

### Read-Only User
**View only, no modifications**

```php
[
    'viewAsset',
    'viewLicense',
    'viewClient',
    'viewUser',
    'viewTicket',
    'viewProject',
    'viewIssue',
    'viewComment',
    'viewFile',
    'viewContact',
    'viewKB',
]
```

---

### End User (Client User)
**Submit tickets, view own assets**

```php
[
    'viewAsset',      // Own assets only (filtered by clientid)
    'viewTicket',     // Own tickets only
    'addTicket',      // Submit new tickets
    'manageTicket',   // Reply to own tickets
    'viewFile',       // View attachments
    'uploadFile',     // Upload attachments
    'viewKB',         // View knowledge base
]
```

---

## Permission Implementation in Laravel

### Create Permission Seeder

```php
// database/seeders/PermissionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Super Admin Role
        Role::create([
            'name' => 'Super Admin',
            'perms' => json_encode([
                'viewAsset', 'addAsset', 'editAsset', 'deleteAsset', 'assetLicense',
                'viewLicense', 'addLicense', 'editLicense', 'deleteLicense',
                'viewClient', 'addClient', 'editClient', 'deleteClient', 'adminsClient',
                'viewUser', 'addUser', 'editUser', 'deleteUser',
                'viewStaff', 'addStaff', 'editStaff', 'deleteStaff',
                'viewTicket', 'addTicket', 'editTicket', 'deleteTicket', 'manageTicket', 'manageTicketNotes',
                'viewProject', 'addProject', 'editProject', 'deleteProject', 'adminsProject', 'manageProjectNotes',
                'viewIssue', 'addIssue', 'editIssue', 'deleteIssue',
                'viewMilestone', 'addMilestone', 'editMilestone', 'deleteMilestone', 'releaseMilestone',
                'viewComment', 'addComment', 'editComment', 'deleteComment',
                'viewFile', 'uploadFile', 'deleteFile',
                'viewCredential', 'addCredential', 'editCredential', 'deleteCredential',
                'viewContact', 'addContact', 'editContact', 'deleteContact',
                'viewHost', 'addHost', 'editHost', 'deleteHost', 'manageHost',
                'viewKB', 'addKB', 'editKB', 'deleteKB',
                'viewTime', 'addTime', 'editTime', 'deleteTime',
                'viewPReply', 'addPReply', 'editPReply', 'deletePReply',
                'viewRole', 'addRole', 'editRole', 'deleteRole',
                'manageData',
                'viewCustomField', 'addCustomField', 'editCustomField', 'deleteCustomField',
                'manageApiKeys',
                'manageSettings',
                'viewSystemLog', 'viewEmailLog', 'viewSMSLog',
            ]),
        ]);
        
        // Support Agent Role
        Role::create([
            'name' => 'Support Agent',
            'perms' => json_encode([
                'viewAsset',
                'viewLicense',
                'viewClient',
                'viewUser',
                'viewTicket', 'addTicket', 'editTicket', 'manageTicket',
                'viewProject',
                'viewIssue',
                'viewComment', 'addComment',
                'viewFile', 'uploadFile',
                'viewContact',
                'viewKB',
                'viewPReply',
            ]),
        ]);
        
        // End User Role
        Role::create([
            'name' => 'End User',
            'perms' => json_encode([
                'viewAsset',
                'viewTicket',
                'addTicket',
                'manageTicket',
                'viewFile',
                'uploadFile',
                'viewKB',
            ]),
        ]);
    }
}
```

### Helper Function

```php
// app/Helpers/PermissionHelper.php
namespace App\Helpers;

class PermissionHelper
{
    public static function hasPermission($permission)
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        $role = $user->role;
        $permissions = json_decode($role->perms, true) ?? [];
        
        return in_array($permission, $permissions);
    }
    
    public static function getAllPermissions()
    {
        return [
            'Asset Management' => [
                'viewAsset' => 'View assets',
                'addAsset' => 'Create new asset',
                'editAsset' => 'Edit existing asset',
                'deleteAsset' => 'Delete asset',
                'assetLicense' => 'Assign/unassign licenses',
            ],
            'License Management' => [
                'viewLicense' => 'View licenses',
                'addLicense' => 'Create new license',
                'editLicense' => 'Edit existing license',
                'deleteLicense' => 'Delete license',
            ],
            // ... more categories
        ];
    }
}
```

---

## Total Permission Count

**Total Permissions:** 80+

**Categories:** 23

**Suggested Roles:** 7

---

**Document Version:** 1.0  
**Last Updated:** 2025-11-22

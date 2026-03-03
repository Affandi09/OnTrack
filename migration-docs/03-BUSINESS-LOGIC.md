# Business Logic Documentation

## Core Business Rules

---

## 1. Ticket System

### Ticket Creation Flow

```
1. User/Guest submits ticket
   ├─ Generate random 6-digit ticket number (unique)
   ├─ Match user by email (if exists)
   ├─ If user found:
   │  ├─ Get user's client
   │  ├─ Get user's assigned asset (first one)
   │  └─ Auto-assign to asset's admin
   └─ If user not found:
      └─ userid = 0, clientid = 0

2. Create ticket record
   ├─ Insert into tickets table
   └─ Insert first reply into tickets_replies

3. Handle attachments (if any)
   └─ Upload files linked to reply

4. Send notifications
   ├─ User notification (if enabled)
   └─ Admin notification (all admins with ticketsnotification=1)

5. Log system activity
```

### Email-to-Ticket Conversion

**Process:**
```
1. Cron job fetches emails from IMAP server
2. Extract email details:
   ├─ From: sender email
   ├─ To: department email (for routing)
   ├─ CC: additional recipients
   ├─ Subject: ticket subject
   └─ Body: ticket message

3. Match sender:
   ├─ Check if user exists (by email)
   ├─ Check if admin exists (by email)
   └─ Get associated client/asset

4. Check if reply or new ticket:
   ├─ Search subject for existing ticket number (######)
   ├─ If found: Add as reply
   └─ If not found: Create new ticket

5. Route to department:
   ├─ Match To: address with department email
   └─ Assign to that department

6. Set status:
   ├─ If from admin: status = "Answered"
   └─ If from user: status = "Reopened" (if existing) or "Open" (if new)
```

**Important Logic:**
- Ticket number must be in subject for matching
- Department routing based on To: email address
- Auto-assign admin if user has assigned asset
- CC emails stored as serialized array

### Ticket Status Workflow

```
Open → In Progress → Answered → Closed
  ↓         ↓           ↓
  └─────────┴───────────┴─→ Reopened → (back to workflow)
```

**Status Timestamps:**
- `respondtime`: First time status changed to "Answered"
- `inprogresstime`: First time status changed to "In Progress"
- `closetime`: First time status changed to "Closed"

**Auto-Close Logic:**
```
IF ticket.status = "Answered"
AND (now - last_reply_time) > auto_close_hours
THEN
  - Change status to "Closed"
  - Send notification (if enabled)
```

### Escalation Rules

**Two Types:**

**1. Global Rules (ticketid = 0)**
```
Conditions:
- Status IN [selected statuses]
- Priority IN [selected priorities]
- Time elapsed > X minutes since last reply

Actions:
- Change status
- Change priority
- Assign to admin
- Add reply
- Notify admins
```

**2. Ticket-Specific Rules (ticketid > 0)**
```
Conditions:
- Status IN [selected statuses]
- Priority IN [selected priorities]
- DateTime >= specific date/time

Actions: (same as global)

Execution:
- Runs once (executed = 1 after running)
```

**Processing:**
- Runs via cron job
- Checks all non-executed rules
- Applies actions if conditions met
- Logs all changes

---

## 2. Asset Management

### Asset Assignment

```
Asset
├─ Belongs to ONE client (required)
├─ Assigned to ONE user (optional)
├─ Managed by ONE admin (optional)
├─ Located at ONE location (optional)
└─ Can have MULTIPLE licenses assigned
```

**Business Rules:**
1. Asset tag must be unique
2. Serial number should be unique (not enforced)
3. Warranty calculated from purchase_date + warranty_months
4. Custom fields stored as serialized array
5. QR code can be attached (one-to-one)

### Asset-License Assignment

```
License (seats = 5)
├─ Asset 1 ✓
├─ Asset 2 ✓
├─ Asset 3 ✓
├─ Asset 4 ✓
├─ Asset 5 ✓
└─ Asset 6 ✗ (exceeds seats)
```

**Business Rules:**
1. Cannot assign more assets than available seats
2. One asset can have multiple licenses
3. One license can be assigned to multiple assets
4. Assignment tracked in `licenses_assets` table

### Auto-Create Attributes

When adding/editing assets, if attribute doesn't exist:
```
Category, Manufacturer, Model, Supplier, Location
└─ Auto-create if name provided but ID not found
   └─ Prevents data entry errors
```

---

## 3. Project Management

### Project Progress Calculation

**Two Modes:**

**1. Manual Mode (progress >= 0)**
```
User sets progress slider: 0-100%
```

**2. Auto Mode (progress = -1)**
```
progress = (done_issues / total_issues) * 100

Example:
- Total issues: 10
- Done issues: 3
- Progress: 30%
```

### Issue Workflow

```
To Do → In Progress → Done
```

**Business Rules:**
1. Issues belong to one project
2. Issues can be assigned to one milestone (optional)
3. Issues can be assigned to one admin (optional)
4. Issues can have multiple comments
5. Time can be logged against issues

### Milestone Release

```
Milestone
├─ releasedate: Target date
└─ released: Boolean flag

When released:
- All issues in milestone should be "Done"
- Milestone marked as released
- Cannot be unreleased
```

---

## 4. License Management

### Serial Number Encryption

```
Storage:
- Encrypted with AES-256-CBC
- Key from config.php

Display:
- Decrypted on-the-fly
- Only visible to authorized users

API:
- Returned encrypted
- Client must decrypt
```

### Seat Management

```
License (seats = 10)
├─ Used: 7 (count from licenses_assets)
└─ Available: 3

Business Rules:
- Cannot assign if used >= seats
- Unassigning frees up a seat
- Seat count can be increased anytime
```

---

## 5. User & Permission System

### User Types

**Admin (type = 'admin')**
- Full system access (based on role)
- Can be assigned to clients
- Can be assigned to projects
- Can manage tickets, assets, etc.

**User (type = 'user')**
- Limited access (based on role)
- Belongs to ONE client
- Can submit tickets
- Can view own assets
- Cannot access other clients' data

### Role-Based Permissions

**Permission Storage:**
```php
roles.perms = serialize([
    'viewAsset',
    'addAsset',
    'editAsset',
    'deleteAsset',
    // ... more permissions
]);
```

**Permission Check:**
```php
if (!in_array('addAsset', $perms)) {
    // Deny access
}
```

**Common Permissions:**
- Asset: viewAsset, addAsset, editAsset, deleteAsset
- License: viewLicense, addLicense, editLicense, deleteLicense
- Ticket: viewTicket, addTicket, editTicket, deleteTicket, manageTicket
- Project: viewProject, addProject, editProject, deleteProject
- User: viewUser, addUser, editUser, deleteUser
- Staff: viewStaff, addStaff, editStaff, deleteStaff
- Client: viewClient, addClient, editClient, deleteClient
- Data: manageData (for attributes)
- Settings: manageSettings
- API: manageApiKeys

### Multi-Tenancy (Client Isolation)

**Data Isolation:**
```
Admin (isAdmin = true)
└─ Can access ALL clients

Admin (isAdmin = false)
├─ Assigned to specific clients (via clients_admins)
└─ Can only access assigned clients

User
└─ Can only access own client (clientid)
```

**Enforcement:**
```php
// Check if user owns the data
if (!$isAdmin && $object['clientid'] != $liu['clientid']) {
    // Deny access
}
```

---

## 6. Authentication & Security

### Password Hashing

**Current (Weak):**
```php
password = SHA1($password)
```

**Should Migrate To:**
```php
password = bcrypt($password) // or password_hash()
```

### Session Management

```
Login:
1. Verify credentials (internal or LDAP)
2. Generate session_id
3. Store session_id in people table
4. Set PHP session

Check:
1. Get session_id from PHP session
2. Query people table for matching session_id
3. If found: authenticated
4. If not found: redirect to login

Logout:
1. Clear session_id from people table
2. Destroy PHP session
```

### LDAP Authentication

```
1. Check if user exists with ldap_user = email
2. If exists:
   ├─ Connect to LDAP server
   ├─ Bind with uid=email,dn
   ├─ If bind successful: login
   └─ If bind fails: deny
3. If not exists:
   └─ Fall back to internal auth
```

### Password Reset

```
1. User requests reset (email)
2. Generate random resetkey (32 chars)
3. Store resetkey in people table
4. Send email with reset link
5. User clicks link with resetkey
6. Verify resetkey exists
7. Allow password change
8. Clear resetkey
```

---

## 7. Notification System

### Email Notifications

**Template Variables:**
```
{ticketid}    - Ticket number
{status}      - Ticket status
{subject}     - Ticket subject
{contact}     - User name
{message}     - Reply message
{company}     - Company name
{appurl}      - Application URL
{client}      - Client name
{department}  - Department name
{email}       - User email
{password}    - User password (for new user)
{resetlink}   - Password reset link
{hostinfo}    - Monitoring host info
```

**Notification Types:**
1. Ticket User Notification (new ticket, reply)
2. Ticket Staff Notification (new ticket, user reply)
3. New User/Admin (welcome email)
4. Password Reset
5. Monitoring Alert

**Sending:**
```
1. Get template from notificationtemplates
2. Replace variables in subject and message
3. Send via PHPMailer (SMTP or mail())
4. Log to emaillog table
5. Send FCM push notification (if fcmtoken exists)
```

### SMS Notifications

**Providers:**
- SMSGlobal
- Clickatell

**Usage:**
- Monitoring alerts
- Critical notifications

---

## 8. Monitoring System

### Host Checks

**Check Types:**
1. **Ping** - ICMP ping
2. **HTTP** - HTTP request (optional keyword check)
3. **HTTPS** - HTTPS request (optional keyword check)
4. **Port** - TCP port check

**Check Process:**
```
1. Cron job runs checks
2. For each host:
   ├─ Run all checks
   ├─ Update status (up/down)
   ├─ If status changed:
   │  ├─ Update laststatus
   │  ├─ Get alert recipients (hosts_people)
   │  ├─ Send email (if enabled)
   │  └─ Send SMS (if enabled)
   └─ Update lastcheck timestamp
```

**Alert Logic:**
```
IF check.status != check.laststatus THEN
  - Status changed (up→down or down→up)
  - Send alerts to assigned people
  - Update laststatus
```

---

## 9. File Management

### File Upload

```
1. Receive file upload
2. Generate filename: {next_id}-{original_name}
3. Move to /uploads directory
4. Insert record to files table
5. Link to: client, project, asset, or ticket reply
```

**File Associations:**
- Client files (general documents)
- Project files (project documents)
- Asset files (manuals, invoices)
- Ticket reply files (attachments)

### File Deletion

```
1. Get file record from database
2. Delete physical file from /uploads
3. Delete database record
4. Log activity
```

**Cascade Delete:**
- When ticket deleted: delete all reply files
- When project deleted: delete all project files

---

## 10. Custom Fields

### Dynamic Fields

**Storage:**
```
assets.customfields = serialize([
    'field_1' => 'value1',
    'field_2' => 'value2',
    // ...
]);
```

**Field Definition:**
```
assets_customfields
├─ id: 1
├─ name: "RAM Size"
└─ type: "text"

assets_customfields
├─ id: 2
├─ name: "Operating System"
└─ type: "select"
```

**Usage:**
```
1. Get all custom field definitions
2. For each field:
   ├─ Render input based on type
   └─ Store value in customfields array
3. Serialize and save
```

---

## 11. QR Code System

### QR Code Generation

```
1. Generate batch of QR codes
2. Each code has unique value
3. Type: 'asset' or 'license'
4. Status: attached = 0 (available)
```

### QR Code Assignment

```
1. Select available QR code
2. Attach to asset or license
3. Update qrvalue in asset/license table
4. Mark QR code as attached = 1
```

### QR Code Scanning

```
1. Scan QR code
2. Get value
3. Search in assets or licenses
4. Display asset/license details
```

---

## 12. Time Logging

### Time Entry

```
timelog
├─ date: Work date
├─ start: Start time (HH:MM)
├─ end: End time (HH:MM)
└─ duration: Calculated (end - start)
```

**Calculation:**
```php
$timestamp1 = strtotime($date . " " . $start);
$timestamp2 = strtotime($date . " " . $end);
$duration = $timestamp2 - $timestamp1; // seconds
```

**Reporting:**
```
Work hours by month:
1. Get all timelog entries for month
2. Sum all durations
3. Convert to hours:minutes
```

---

## 13. Knowledge Base

### Article Structure

```
kb_categories
└─ kb_articles
   ├─ title
   ├─ content (HTML)
   └─ timestamp
```

**Search:**
- Search in title and content
- Filter by category

---

## 14. Import System

### Asset Import

```
1. Upload CSV file
2. Parse CSV
3. For each row:
   ├─ Validate required fields
   ├─ Auto-create attributes (if needed)
   ├─ Create asset record
   └─ Log errors
4. Return summary
```

**CSV Format:**
```
name,tag,serial,category,manufacturer,model,client,...
```

### License Import

Similar to asset import, but for licenses.

---

## Migration Considerations

### Complex Business Logic to Preserve

1. **Email-to-Ticket Parsing**
   - Subject matching for replies
   - Department routing
   - User/admin detection

2. **Escalation Rules**
   - Time-based triggers
   - Condition matching
   - Action execution

3. **Multi-Tenancy**
   - Client isolation
   - Permission checks
   - Data filtering

4. **Auto-Create Attributes**
   - Prevents data entry errors
   - Maintains referential integrity

5. **Notification System**
   - Template variables
   - Multiple channels
   - Conditional sending

### Recommended Improvements

1. **Replace Serialized Data**
   - Use JSON instead of PHP serialize()
   - Or create proper relational tables

2. **Improve Password Security**
   - Migrate from SHA1 to bcrypt
   - Add password policies

3. **Add CSRF Protection**
   - Laravel has built-in CSRF

4. **Add Input Validation**
   - Laravel Form Requests
   - Validation rules

5. **Add API Rate Limiting**
   - Prevent abuse
   - Laravel has built-in rate limiting

6. **Add Audit Trail**
   - Track all changes
   - Who, what, when

7. **Add Soft Deletes**
   - Don't permanently delete
   - Allow recovery

---

**Document Version:** 1.0  
**Last Updated:** 2025-11-22

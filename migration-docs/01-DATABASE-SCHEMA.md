# Database Schema Documentation

## Complete Table Structure

Based on code analysis, here are all database tables with their fields and relationships.

---

## Core Entity Tables

### people
**Purpose:** Users and Staff (Admins)

```sql
CREATE TABLE people (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    type ENUM('user', 'admin'),
    roleid INT,
    clientid INT,
    email VARCHAR(255) UNIQUE,
    ldap_user VARCHAR(255),
    title VARCHAR(255),
    mobile VARCHAR(50),
    password VARCHAR(255), -- SHA1 hash
    theme VARCHAR(50),
    sidebar VARCHAR(50),
    layout VARCHAR(50),
    notes TEXT,
    signature TEXT,
    sessionid VARCHAR(255),
    resetkey VARCHAR(255),
    avatar BLOB, -- Binary image data
    autorefresh INT,
    lang VARCHAR(10),
    ticketsnotification TINYINT(1),
    fcmtoken VARCHAR(255),
    INDEX idx_email (email),
    INDEX idx_sessionid (sessionid),
    INDEX idx_type (type),
    INDEX idx_clientid (clientid)
);
```

**Relationships:**
- `roleid` → roles.id
- `clientid` → clients.id

---

### clients
**Purpose:** Organizations/Companies

```sql
CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    asset_tag_prefix VARCHAR(50),
    license_tag_prefix VARCHAR(50),
    notes TEXT,
    INDEX idx_name (name)
);
```

---

### assets
**Purpose:** IT Assets (computers, phones, equipment)

```sql
CREATE TABLE assets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    categoryid INT,
    adminid INT, -- Responsible staff
    clientid INT,
    userid INT, -- Assigned user
    manufacturerid INT,
    modelid INT,
    supplierid INT,
    statusid INT,
    locationid INT,
    purchase_date DATE,
    warranty_months INT,
    tag VARCHAR(100),
    name VARCHAR(255),
    serial VARCHAR(255),
    notes TEXT,
    customfields TEXT, -- Serialized array
    qrvalue VARCHAR(255),
    peoplename VARCHAR(255), -- Denormalized user name
    INDEX idx_clientid (clientid),
    INDEX idx_userid (userid),
    INDEX idx_tag (tag),
    INDEX idx_serial (serial)
);
```

**Relationships:**
- `categoryid` → assetcategories.id
- `adminid` → people.id (type='admin')
- `clientid` → clients.id
- `userid` → people.id (type='user')
- `manufacturerid` → manufacturers.id
- `modelid` → models.id
- `supplierid` → suppliers.id
- `statusid` → statuslabels.id
- `locationid` → locations.id

---

### licenses
**Purpose:** Software Licenses

```sql
CREATE TABLE licenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clientid INT,
    statusid INT,
    categoryid INT,
    supplierid INT,
    seats INT,
    tag VARCHAR(100),
    name VARCHAR(255),
    serial TEXT, -- Encrypted
    notes TEXT,
    customfields TEXT, -- Serialized array
    qrvalue VARCHAR(255),
    INDEX idx_clientid (clientid),
    INDEX idx_tag (tag)
);
```

**Relationships:**
- `clientid` → clients.id
- `statusid` → statuslabels.id
- `categoryid` → licensecategories.id
- `supplierid` → suppliers.id

---

### tickets
**Purpose:** Support Tickets

```sql
CREATE TABLE tickets (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    ticket INT(11) NOT NULL, -- Random 6-digit number (stored as INT, not VARCHAR)
    departmentid INT(11) NOT NULL,
    clientid INT(11) NOT NULL,
    userid INT(11) NOT NULL,
    adminid INT(11) NOT NULL, -- Assigned staff
    assetid INT(11) NOT NULL,
    projectid INT(11) NOT NULL,
    email VARCHAR(128) NOT NULL,
    subject VARCHAR(500) NOT NULL, -- Longer than expected (500 chars)
    status VARCHAR(50) NOT NULL, -- Stored as VARCHAR, not ENUM
    priority VARCHAR(50) NOT NULL, -- Stored as VARCHAR, not ENUM
    timestamp DATETIME NOT NULL,
    notes LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    ccs VARCHAR(255) NOT NULL, -- Serialized array of CC emails
    timespent INT(10) NOT NULL, -- Minutes
    respondtime DATETIME DEFAULT NULL, -- First response time
    inprogresstime DATETIME DEFAULT NULL, -- When moved to In Progress
    closetime DATETIME DEFAULT NULL, -- When closed
    INDEX idx_ticket (ticket),
    INDEX idx_clientid (clientid),
    INDEX idx_userid (userid),
    INDEX idx_adminid (adminid),
    INDEX idx_status (status),
    INDEX idx_timestamp (timestamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
```

**Important Notes:**
- `ticket` is INT(11), not VARCHAR(6) as initially assumed
- `subject` allows up to 500 characters
- `status` and `priority` are VARCHAR, not ENUM (more flexible)
- `notes` uses utf8mb4 charset (supports emoji/special chars)
- Engine is MyISAM (should migrate to InnoDB for Laravel)

**Relationships:**
- `departmentid` → tickets_departments.id
- `clientid` → clients.id
- `userid` → people.id
- `adminid` → people.id
- `assetid` → assets.id
- `projectid` → projects.id

---

### tickets_replies
**Purpose:** Ticket conversation

```sql
CREATE TABLE tickets_replies (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    ticketid INT(11) NOT NULL,
    peopleid INT(11) NOT NULL, -- Can be 0 for guest
    message TEXT NOT NULL,
    timestamp DATETIME NOT NULL,
    INDEX idx_ticketid (ticketid),
    INDEX idx_timestamp (timestamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
```

**Relationships:**
- `ticketid` → tickets.id
- `peopleid` → people.id (0 = guest)

---

### tickets_actions
**Purpose:** Ticket Actions Log (NEW - found in SQL dump)

```sql
CREATE TABLE tickets_actions (
    id INT(15) PRIMARY KEY AUTO_INCREMENT,
    ticketid INT(15) NOT NULL,
    peopleid INT(15) NOT NULL,
    message LONGTEXT NOT NULL,
    timestamp DATETIME NOT NULL,
    INDEX idx_ticketid (ticketid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**Relationships:**
- `ticketid` → tickets.id
- `peopleid` → people.id

**Note:** This table uses InnoDB engine (different from most tables)

---

### tickets_rules
**Purpose:** Escalation Rules

```sql
CREATE TABLE tickets_rules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticketid INT, -- 0 = global rule
    executed TINYINT(1),
    name VARCHAR(255),
    cond_status TEXT, -- Serialized array
    cond_priority TEXT, -- Serialized array
    cond_timeelapsed INT, -- Minutes
    cond_datetime DATETIME,
    act_status VARCHAR(50),
    act_priority VARCHAR(50),
    act_assignto INT,
    act_notifyadmins TINYINT(1),
    act_addreply TINYINT(1),
    reply TEXT,
    INDEX idx_ticketid (ticketid),
    INDEX idx_executed (executed)
);
```

---

### projects
**Purpose:** Projects

```sql
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clientid INT,
    tag VARCHAR(100),
    name VARCHAR(255),
    notes TEXT,
    description TEXT,
    startdate DATE,
    deadline DATE,
    progress INT, -- -1 = auto-calculate, 0-100 = manual
    INDEX idx_clientid (clientid),
    INDEX idx_tag (tag)
);
```

**Relationships:**
- `clientid` → clients.id

---

### issues
**Purpose:** Project Tasks/Issues

```sql
CREATE TABLE issues (
    id INT PRIMARY KEY AUTO_INCREMENT,
    projectid INT,
    milestoneid INT,
    adminid INT, -- Assigned staff
    clientid INT,
    tag VARCHAR(100),
    name VARCHAR(255),
    description TEXT,
    status ENUM('To Do', 'In Progress', 'Done'),
    priority ENUM('Low', 'Normal', 'High'),
    timestamp DATETIME,
    INDEX idx_projectid (projectid),
    INDEX idx_milestoneid (milestoneid),
    INDEX idx_status (status)
);
```

**Relationships:**
- `projectid` → projects.id
- `milestoneid` → milestones.id
- `adminid` → people.id
- `clientid` → clients.id

---

### milestones
**Purpose:** Project Milestones

```sql
CREATE TABLE milestones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    projectid INT,
    name VARCHAR(255),
    description TEXT,
    releasedate DATE,
    released TINYINT(1),
    INDEX idx_projectid (projectid)
);
```

**Relationships:**
- `projectid` → projects.id

---

### comments
**Purpose:** Issue Comments

```sql
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    issueid INT,
    peopleid INT,
    comment TEXT,
    timestamp DATETIME,
    INDEX idx_issueid (issueid),
    INDEX idx_timestamp (timestamp)
);
```

**Relationships:**
- `issueid` → issues.id
- `peopleid` → people.id

---

### files
**Purpose:** File Attachments

```sql
CREATE TABLE files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clientid INT,
    projectid INT,
    assetid INT,
    ticketreplyid INT,
    name VARCHAR(255),
    file VARCHAR(255), -- Filename on disk
    INDEX idx_clientid (clientid),
    INDEX idx_projectid (projectid),
    INDEX idx_assetid (assetid),
    INDEX idx_ticketreplyid (ticketreplyid)
);
```

**Relationships:**
- `clientid` → clients.id
- `projectid` → projects.id
- `assetid` → assets.id
- `ticketreplyid` → tickets_replies.id

---

### timelog
**Purpose:** Time Tracking

```sql
CREATE TABLE timelog (
    id INT PRIMARY KEY AUTO_INCREMENT,
    projectid INT,
    issueid INT,
    clientid INT,
    peopleid INT,
    date DATE,
    start TIME,
    end TIME,
    description TEXT,
    INDEX idx_projectid (projectid),
    INDEX idx_issueid (issueid),
    INDEX idx_date (date)
);
```

**Relationships:**
- `projectid` → projects.id
- `issueid` → issues.id
- `clientid` → clients.id
- `peopleid` → people.id

---

## Monitoring Tables

### hosts
**Purpose:** Monitored Hosts

```sql
CREATE TABLE hosts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clientid INT,
    name VARCHAR(255),
    ip VARCHAR(50),
    notes TEXT,
    INDEX idx_clientid (clientid)
);
```

---

### checks
**Purpose:** Monitoring Checks

```sql
CREATE TABLE checks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hostid INT,
    type VARCHAR(50), -- ping, http, https, port
    port INT,
    keyword VARCHAR(255),
    status VARCHAR(50),
    laststatus VARCHAR(50),
    lastcheck DATETIME,
    INDEX idx_hostid (hostid)
);
```

**Relationships:**
- `hostid` → hosts.id

---

### hosts_people
**Purpose:** Monitoring Alert Recipients

```sql
CREATE TABLE hosts_people (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    hostid INT(11) NOT NULL,
    peopleid INT(11) NOT NULL,
    email TINYINT(1) NOT NULL,
    sms TINYINT(1) NOT NULL,
    INDEX idx_hostid (hostid),
    INDEX idx_peopleid (peopleid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
```

**Relationships:**
- `hostid` → hosts.id
- `peopleid` → people.id

---

### hosts_history
**Purpose:** Monitoring Check History (NEW - found in SQL dump)

```sql
CREATE TABLE hosts_history (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    checkid INT(11) NOT NULL,
    status VARCHAR(50) NOT NULL,
    timestamp DATETIME NOT NULL,
    INDEX idx_checkid (checkid),
    INDEX idx_timestamp (timestamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
```

**Relationships:**
- `checkid` → hosts_checks.id

**Purpose:** Stores historical status changes for monitoring checks

---

## Lookup/Attribute Tables

### assetcategories
```sql
CREATE TABLE assetcategories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    color VARCHAR(7) -- Hex color
);
```

### licensecategories
```sql
CREATE TABLE licensecategories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    color VARCHAR(7)
);
```

### statuslabels
```sql
CREATE TABLE statuslabels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    color VARCHAR(7),
    type VARCHAR(50) -- 'asset' or 'license'
);
```

### manufacturers
```sql
CREATE TABLE manufacturers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255)
);
```

### models
```sql
CREATE TABLE models (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255)
);
```

### suppliers
```sql
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255)
);
```

### locations
```sql
CREATE TABLE locations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clientid INT,
    name VARCHAR(255),
    INDEX idx_clientid (clientid)
);
```

**Relationships:**
- `clientid` → clients.id

### tickets_departments
```sql
CREATE TABLE tickets_departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    email VARCHAR(255) -- For email-to-ticket routing
);
```

---

## Relationship Tables

### licenses_assets
**Purpose:** Many-to-Many License-Asset Assignment

```sql
CREATE TABLE licenses_assets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    licenseid INT,
    assetid INT,
    INDEX idx_licenseid (licenseid),
    INDEX idx_assetid (assetid),
    UNIQUE KEY unique_assignment (licenseid, assetid)
);
```

**Relationships:**
- `licenseid` → licenses.id
- `assetid` → assets.id

---

### clients_admins
**Purpose:** Many-to-Many Client-Staff Assignment

```sql
CREATE TABLE clients_admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clientid INT,
    adminid INT,
    INDEX idx_clientid (clientid),
    INDEX idx_adminid (adminid),
    UNIQUE KEY unique_assignment (clientid, adminid)
);
```

**Relationships:**
- `clientid` → clients.id
- `adminid` → people.id (type='admin')

---

### projects_admins
**Purpose:** Many-to-Many Project-Staff Assignment

```sql
CREATE TABLE projects_admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    projectid INT,
    adminid INT,
    INDEX idx_projectid (projectid),
    INDEX idx_adminid (adminid),
    UNIQUE KEY unique_assignment (projectid, adminid)
);
```

**Relationships:**
- `projectid` → projects.id
- `adminid` → people.id (type='admin')

---

## System Tables

### roles
**Purpose:** User Roles and Permissions

```sql
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    perms TEXT -- Serialized array of permission strings
);
```

---

### config
**Purpose:** Application Configuration

```sql
CREATE TABLE config (
    name VARCHAR(255) PRIMARY KEY,
    value TEXT
);
```

**Key Configuration Values:**
- app_name, app_url, company_name, company_details
- timezone, date_format, default_lang, week_start
- email_* (SMTP settings)
- sms_* (SMS provider settings)
- ldap_* (LDAP settings)
- tickets_* (Ticket system settings)
- recaptcha_* (reCAPTCHA settings)
- log_retention, table_records
- asset_tag_prefix, license_tag_prefix
- password_generator_length
- label_width, label_height, label_qrsize
- manual_qrvalue, auto_close_tickets

---

### statuscodes
**Purpose:** Status Messages for User Feedback

```sql
CREATE TABLE statuscodes (
    code INT PRIMARY KEY,
    message TEXT
);
```

---

### notificationtemplates
**Purpose:** Email/SMS Templates

```sql
CREATE TABLE notificationtemplates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    subject VARCHAR(255),
    message TEXT,
    sms TEXT
);
```

**Template Variables:**
- {ticketid}, {status}, {subject}, {contact}, {message}
- {company}, {appurl}, {client}, {department}
- {email}, {password}, {resetlink}
- {hostinfo}

---

### api_keys
**Purpose:** API Authentication

```sql
CREATE TABLE api_keys (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    secretkey VARCHAR(255) UNIQUE,
    roleid INT,
    INDEX idx_secretkey (secretkey)
);
```

**Relationships:**
- `roleid` → roles.id

---

### systemlog
**Purpose:** System Activity Log

```sql
CREATE TABLE systemlog (
    id INT PRIMARY KEY AUTO_INCREMENT,
    peopleid INT, -- -1 for system actions
    ipaddress VARCHAR(50),
    description TEXT,
    timestamp DATETIME,
    INDEX idx_peopleid (peopleid),
    INDEX idx_timestamp (timestamp)
);
```

---

### emaillog
**Purpose:** Email Sending Log

```sql
CREATE TABLE emaillog (
    id INT PRIMARY KEY AUTO_INCREMENT,
    peopleid INT,
    clientid INT,
    to VARCHAR(255),
    subject VARCHAR(255),
    message TEXT,
    timestamp DATETIME,
    INDEX idx_timestamp (timestamp)
);
```

---

### smslog
**Purpose:** SMS Sending Log

```sql
CREATE TABLE smslog (
    id INT PRIMARY KEY AUTO_INCREMENT,
    peopleid INT,
    clientid INT,
    mobile VARCHAR(50),
    sms TEXT,
    timestamp DATETIME,
    INDEX idx_timestamp (timestamp)
);
```

---

## Custom Fields

### assets_customfields
**Purpose:** Dynamic Asset Fields Definition

```sql
CREATE TABLE assets_customfields (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    type VARCHAR(50) -- text, textarea, select, etc.
);
```

---

### licenses_customfields
**Purpose:** Dynamic License Fields Definition

```sql
CREATE TABLE licenses_customfields (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    type VARCHAR(50)
);
```

---

## Knowledge Base

### kb_categories
```sql
CREATE TABLE kb_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    description TEXT
);
```

---

### kb_articles
```sql
CREATE TABLE kb_articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    categoryid INT,
    title VARCHAR(255),
    content TEXT,
    timestamp DATETIME,
    INDEX idx_categoryid (categoryid)
);
```

**Relationships:**
- `categoryid` → kb_categories.id

---

## QR Codes

### qrcodes
**Purpose:** QR Code Management

```sql
CREATE TABLE qrcodes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    batchid INT,
    value VARCHAR(255),
    type VARCHAR(50), -- 'asset' or 'license'
    attached TINYINT(1),
    INDEX idx_batchid (batchid),
    INDEX idx_value (value)
);
```

---

## Predefined Replies

### tickets_pr
**Purpose:** Canned Responses for Tickets (Predefined Replies)

```sql
CREATE TABLE tickets_pr (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    content LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
```

**Note:** 
- Table name is `tickets_pr`, not `predefined_replies`
- Field is `content`, not `reply`
- Uses LONGTEXT with utf8mb4 charset

---

## Contacts

### contacts
**Purpose:** Client Contacts

```sql
CREATE TABLE contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clientid INT,
    name VARCHAR(255),
    email VARCHAR(255),
    mobile VARCHAR(50),
    notes TEXT,
    INDEX idx_clientid (clientid)
);
```

**Relationships:**
- `clientid` → clients.id

---

## Credentials

### credentials
**Purpose:** Stored Credentials (passwords, API keys, etc.)

```sql
CREATE TABLE credentials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clientid INT,
    name VARCHAR(255),
    username VARCHAR(255),
    password TEXT, -- Encrypted
    notes TEXT,
    INDEX idx_clientid (clientid)
);
```

**Relationships:**
- `clientid` → clients.id

---

## Entity Relationship Diagram (Text)

```
clients
  ├─ people (users, admins)
  ├─ assets
  ├─ licenses
  ├─ tickets
  ├─ projects
  ├─ locations
  ├─ contacts
  ├─ credentials
  └─ hosts

assets
  ├─ people (userid - assigned user)
  ├─ people (adminid - responsible staff)
  ├─ assetcategories
  ├─ manufacturers
  ├─ models
  ├─ suppliers
  ├─ statuslabels (labels table)
  ├─ locations
  └─ licenses (via licenses_assets)

tickets
  ├─ people (userid)
  ├─ people (adminid)
  ├─ tickets_departments
  ├─ assets
  ├─ projects
  ├─ tickets_replies
  │   └─ files
  └─ tickets_actions (NEW - action log)

projects
  ├─ people (via projects_admins)
  ├─ issues
  │   ├─ milestones
  │   ├─ comments
  │   └─ timelog
  └─ files

hosts (monitoring)
  ├─ hosts_checks
  │   └─ hosts_history (NEW - check history)
  └─ hosts_people (alert recipients)

people
  ├─ roles
  └─ clients
```

---

## Migration Notes

### Serialized Data to Migrate
1. **roles.perms** → JSON or separate permissions table
2. **assets.customfields** → JSON or separate table
3. **licenses.customfields** → JSON or separate table
4. **tickets.ccs** → JSON or separate table

### Encrypted Data
1. **licenses.serial** → Re-encrypt with Laravel encryption
2. **credentials.password** → Re-encrypt with Laravel encryption

### Denormalized Data
1. **assets.peoplename** → Can be removed, use JOIN instead

### Weak Security
1. **people.password** → Re-hash with bcrypt (SHA1 → bcrypt)

---

## Important Findings from Real Database

### Database Engine
- **Most tables:** MyISAM (needs migration to InnoDB)
- **Exception:** `tickets_actions` uses InnoDB
- **Recommendation:** Convert all to InnoDB for Laravel (better transaction support, foreign keys)

### Charset & Collation
- **Default:** utf8 / utf8_general_ci
- **Some fields:** utf8mb4 / utf8mb4_general_ci (tickets.notes, tickets_pr.content)
- **Recommendation:** Migrate all to utf8mb4 for full Unicode support (emoji, special chars)

### Data Types
- **ticket number:** INT(11), not VARCHAR(6) as initially assumed
- **subject:** VARCHAR(500), longer than expected
- **status/priority:** VARCHAR(50), not ENUM (more flexible)
- **notes:** LONGTEXT with utf8mb4

### New Tables Found
1. **tickets_actions** - Action/activity log for tickets
2. **hosts_history** - Historical monitoring check data
3. **tickets_pr** - Predefined replies (not `predefined_replies`)

### Field Naming
- Table name: `tickets_pr` (not `predefined_replies`)
- Field name: `content` (not `reply`)
- Field name: `labels` table (not `statuslabels` - need to verify)

### Sample Data Statistics
- **76 Asset Categories** (very comprehensive)
- **100+ Tickets** with real data
- **200+ Assets** with assignments
- **Multiple clients** with real operations

---

**Document Version:** 1.1  
**Last Updated:** 2025-11-22  
**Updated:** Added findings from real SQL dump (33MB production database)

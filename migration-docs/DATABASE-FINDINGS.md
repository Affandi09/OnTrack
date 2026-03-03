# Database Analysis - Real SQL Dump Findings

## Source Information

**File:** `it_helpdesk.sql`  
**Size:** 33 MB  
**Generated:** November 22, 2025 at 03:11 AM  
**Server:** MariaDB 10.4.27  
**PHP Version:** 7.4.33  
**Database:** `it_helpdesk`

---

## Key Findings

### 1. Database Engine

**Current State:**
- **Majority:** MyISAM engine
- **Exception:** `tickets_actions` uses InnoDB

**Tables using MyISAM:**
- api_keys
- assetcategories
- assets
- assets_customfields
- clients
- clients_admins
- comments
- config
- contacts
- credentials
- emaillog
- files
- hosts
- hosts_checks
- hosts_people
- issues
- kb_articles
- kb_categories
- labels
- languages
- licensecategories
- licenses
- licenses_assets
- licenses_customfields
- locations
- manufacturers
- milestones
- models
- notificationtemplates
- people
- projects
- projects_admins
- roles
- smslog
- statuscodes
- suppliers
- systemlog
- tickets
- tickets_departments
- tickets_pr
- tickets_replies
- tickets_rules
- timelog

**Tables using InnoDB:**
- tickets_actions (only one!)

**Migration Recommendation:**
```sql
-- Convert all tables to InnoDB for Laravel
ALTER TABLE table_name ENGINE=InnoDB;
```

**Why InnoDB?**
- ✅ Foreign key constraints support
- ✅ Transaction support (ACID compliance)
- ✅ Better crash recovery
- ✅ Row-level locking (better concurrency)
- ✅ Laravel default engine

---

### 2. Charset & Collation

**Default:**
- Charset: `utf8`
- Collation: `utf8_general_ci`

**Exceptions (utf8mb4):**
- `tickets.notes` - LONGTEXT utf8mb4
- `tickets_pr.content` - LONGTEXT utf8mb4

**Migration Recommendation:**
```sql
-- Convert all to utf8mb4 for full Unicode support
ALTER TABLE table_name CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Why utf8mb4?**
- ✅ Full Unicode support (4-byte characters)
- ✅ Emoji support 😊
- ✅ Special characters support
- ✅ Laravel default charset

---

### 3. Data Type Discoveries

#### Tickets Table

**Unexpected findings:**

| Field | Expected | Actual | Impact |
|-------|----------|--------|--------|
| `ticket` | VARCHAR(6) | INT(11) | Need to adjust validation |
| `subject` | VARCHAR(255) | VARCHAR(500) | Longer subjects allowed |
| `status` | ENUM | VARCHAR(50) | More flexible, no schema change needed |
| `priority` | ENUM | VARCHAR(50) | More flexible, no schema change needed |
| `notes` | TEXT | LONGTEXT utf8mb4 | Larger content, emoji support |

**Sample ticket number:** 101153, 719032, 891057 (6-digit integers)

---

### 4. New Tables Discovered

#### tickets_actions
**Purpose:** Action/Activity log for tickets

```sql
CREATE TABLE tickets_actions (
    id INT(15) NOT NULL,
    ticketid INT(15) NOT NULL,
    peopleid INT(15) NOT NULL,
    message LONGTEXT NOT NULL,
    timestamp DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Usage:** Logs all actions performed on tickets (status changes, assignments, etc.)

---

#### hosts_history
**Purpose:** Historical monitoring check data

```sql
CREATE TABLE hosts_history (
    id INT(11) NOT NULL,
    checkid INT(11) NOT NULL,
    status VARCHAR(50) NOT NULL,
    timestamp DATETIME NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```

**Usage:** Stores historical status changes for monitoring checks

---

### 5. Table Name Corrections

| Documentation | Actual | Notes |
|---------------|--------|-------|
| `predefined_replies` | `tickets_pr` | Shorter name |
| `statuslabels` | `labels` | Need to verify |

**Field Name Corrections:**

| Table | Documentation | Actual |
|-------|---------------|--------|
| `tickets_pr` | `reply` | `content` |

---

### 6. Sample Data Statistics

#### Asset Categories (76 total)
```
Desktops, Laptops, Monitor, Printers, Routers, MAXHUB, UPS, TAS, 
HARDISK, USB FLASDISK, POS, Handpone, TABLET, AC, Access Points, 
Air Purifier, Akrilik, Alas CPU, Alat Deteksi, Alat Pel, APAR, 
Apron, Artificial, Alat Deteksi Suhu, AVR Stabilizer, 
Bel Calling System, Blower AC Kaset, Box Pendingin, 
Brecket Monitor, Brankas Uang, Bufet laci, Cash Drawer, CCTV, 
Chest Freezer, Rack, DVR, Freezer, Kursi, Lemari, Meja, SERVER, 
Sofa, Timbangan, Bangku, TV, Pesawat Telepon, HT, GENSET, 
DISPENSER, LABEL SCANNER, IPAD, PC, ORBIT, DOMAIN, SSL, HOSTING, 
Proyektor, Bench, Coffee Maker, Hydrant, Kulkas, Laci Padestal, 
Mesin Absensi, Mesin Penghancur Kertas, Papan Tulis, Rak, 
Scanner, Showcase, Speaker, Videotron, Switch
```

**Observation:** Very comprehensive asset categorization

---

#### Tickets (100+ records)

**Status Distribution:**
- Closed: Majority
- Open: Active tickets
- In Progress: Being worked on
- Answered: Waiting for user response

**Priority Distribution:**
- Normal: Most common
- High: Urgent issues
- Low: Not observed in sample

**Common Issues:**
- Printer problems
- Internet/LAN connectivity
- Software installation
- Hardware maintenance

**Sample Subjects:**
- "Mohon cek printer tidak bisa print"
- "Minta tolong Perpindahan Data Onedrive"
- "Export data penggunaan mesin fcopy canon samafitro"
- "Sharing printer ke Rika"
- "PRINTER MATI"
- "Koneksi Internet LAN"

**Observation:** Real production data with Indonesian language

---

#### Assets (200+ records)

**Sample Data:**
- Laptops: Various brands (Asus, HP, Dell, Lenovo)
- Monitors: Multiple units
- Serial numbers: Real device serials
- Assignments: Real user assignments

**Tag Format:** 
- SNJ-XXX (e.g., SNJ-115, SNJ-114)
- GFI (special tag)

---

#### People/Users

**Sample Users:**
- superadmin (admin)
- Rido Anggara (admin)
- Reggy Pasya (admin)
- Dimas Ananda (admin)
- Multiple regular users

**Password Hash:** SHA1 (needs upgrade to bcrypt)

**Sample Hash:** `4b7fb1d0a5d0c1da30e3edfc952da803e7479ed4`

---

### 7. API Keys

**Sample:**
```
Name: Dimas Ananda
Key: GENYP8YyTAErpQPYbKDCGC38bs5QJ1sPeXj22wPuopNseluu8pbnv1QjEXd3ScPV
Role ID: 1
```

**Key Length:** 64 characters (random string)

---

### 8. Configuration Values

**Sample Config (from INSERT statements):**
- app_name
- app_url
- company_name
- timezone
- date_format
- email_smtp_* (SMTP settings)
- ldap_* (LDAP settings)
- tickets_* (Ticket settings)

**Observation:** Extensive configuration system

---

### 9. Field Size Analysis

#### VARCHAR Sizes

| Field | Size | Usage |
|-------|------|-------|
| `tickets.subject` | 500 | Long subjects allowed |
| `tickets.email` | 128 | Standard email length |
| `tickets.ccs` | 255 | Serialized CC list |
| `people.name` | 128 | User names |
| `people.email` | 128 | User emails |
| `people.password` | 128 | SHA1 hash (40 chars) + future-proof |

---

### 10. Timestamp Fields

**Pattern:**
- All use `DATETIME` type
- Format: `YYYY-MM-DD HH:MM:SS`
- Example: `2025-07-04 11:14:40`

**Special Timestamp Fields in Tickets:**
- `timestamp` - Ticket creation
- `respondtime` - First response (NULL if not responded)
- `inprogresstime` - When moved to In Progress (NULL if not)
- `closetime` - When closed (NULL if not closed)

**Observation:** Good for SLA tracking

---

### 11. Serialized Data Examples

#### tickets.ccs
```php
a:1:{i:0;s:0:"";}  // Empty CC
```

**Format:** PHP serialize() format

**Migration Strategy:**
```php
// Old (PHP)
$ccs = serialize(['email1@example.com', 'email2@example.com']);

// New (Laravel)
$ccs = json_encode(['email1@example.com', 'email2@example.com']);
```

---

### 12. NULL vs Empty String

**Observation:**
- Some fields use `DEFAULT NULL`
- Some fields use `NOT NULL` with empty string default
- Inconsistent pattern

**Examples:**
- `tickets.respondtime` - DEFAULT NULL ✅
- `tickets.notes` - NOT NULL (empty string) ❌

**Recommendation:** Use NULL for optional fields in Laravel

---

### 13. Index Analysis

**Current Indexes:**
- Primary keys on all tables
- No visible foreign key constraints (MyISAM limitation)
- No visible secondary indexes in CREATE TABLE statements

**Recommendation for Laravel:**
```php
// Add indexes for foreign keys
$table->index('clientid');
$table->index('userid');
$table->index('adminid');
$table->index('ticketid');

// Add composite indexes for common queries
$table->index(['clientid', 'status']);
$table->index(['userid', 'timestamp']);
```

---

### 14. Data Integrity Issues

**Potential Issues:**

1. **No Foreign Key Constraints**
   - MyISAM doesn't support FK
   - Risk of orphaned records
   - Need to add in Laravel migrations

2. **Inconsistent NULL Handling**
   - Some fields allow NULL
   - Some use empty strings
   - Need to standardize

3. **No Default Values**
   - Most fields don't have defaults
   - Can cause issues with partial inserts

4. **Denormalized Data**
   - `assets.peoplename` duplicates `people.name`
   - Risk of data inconsistency

---

### 15. Security Observations

**Password Storage:**
- Algorithm: SHA1 (weak!)
- No salt visible
- Need to migrate to bcrypt

**Encrypted Fields:**
- `licenses.serial` - Encrypted
- `credentials.password` - Encrypted
- Encryption method: AES-256-CBC (from config.php)

**Session Management:**
- `people.sessionid` - Stores PHP session ID
- `people.resetkey` - Password reset token

---

### 16. Multi-Language Support

**Evidence:**
- `people.lang` field (2-char code)
- `languages` table exists
- Sample: 'en' (English)

**Observation:** System supports multiple languages

---

### 17. File Upload System

**Evidence:**
- `files` table with `file` field (filename)
- Naming pattern: `{id}-{original_filename}`
- Storage: `/uploads` directory

---

### 18. Monitoring System

**Tables:**
- `hosts` - Monitored servers/devices
- `hosts_checks` - Check configurations
- `hosts_history` - Historical data
- `hosts_people` - Alert recipients

**Check Types:**
- ping
- http
- https
- port

---

### 19. Time Tracking

**Evidence:**
- `timelog` table
- Fields: date, start, end
- Linked to: projects, issues, people

**Calculation:**
```php
$duration = strtotime($end) - strtotime($start);
```

---

### 20. Notification System

**Evidence:**
- `notificationtemplates` table
- `emaillog` table (sent emails)
- `smslog` table (sent SMS)

**Template Variables:**
- {ticketid}, {status}, {subject}, {contact}, {message}
- {company}, {appurl}, {client}, {department}

---

## Migration Priority

### High Priority
1. ✅ Convert MyISAM → InnoDB
2. ✅ Convert utf8 → utf8mb4
3. ✅ Add foreign key constraints
4. ✅ Migrate SHA1 → bcrypt passwords
5. ✅ Convert PHP serialize() → JSON

### Medium Priority
1. ⚠️ Add indexes for performance
2. ⚠️ Standardize NULL handling
3. ⚠️ Add default values
4. ⚠️ Remove denormalized data

### Low Priority
1. 📝 Optimize field sizes
2. 📝 Add composite indexes
3. 📝 Add check constraints

---

## Laravel Migration Considerations

### 1. Engine Conversion
```php
Schema::create('tickets', function (Blueprint $table) {
    $table->engine = 'InnoDB'; // Force InnoDB
});
```

### 2. Charset
```php
Schema::create('tickets', function (Blueprint $table) {
    $table->charset = 'utf8mb4';
    $table->collation = 'utf8mb4_unicode_ci';
});
```

### 3. Foreign Keys
```php
$table->foreignId('clientid')->constrained('clients')->onDelete('cascade');
$table->foreignId('userid')->constrained('people')->onDelete('set null');
```

### 4. Indexes
```php
$table->index('status');
$table->index('timestamp');
$table->index(['clientid', 'status']);
```

### 5. JSON Fields
```php
$table->json('ccs')->nullable(); // Instead of VARCHAR(255)
$table->json('customfields')->nullable(); // Instead of TEXT
```

---

## Data Migration Script Considerations

### 1. Password Migration
```php
// Users must reset password after migration
// Or: attempt to verify old SHA1 and upgrade on first login
```

### 2. Serialized Data
```php
// Convert PHP serialize to JSON
$old = unserialize($data);
$new = json_encode($old);
```

### 3. Ticket Numbers
```php
// Already INT, no conversion needed
// But ensure uniqueness constraint
```

### 4. Timestamps
```php
// Already in correct format
// But add created_at, updated_at for Laravel
```

---

## Recommendations Summary

### Must Do
1. Convert all tables to InnoDB
2. Convert all to utf8mb4
3. Add foreign key constraints
4. Migrate passwords to bcrypt
5. Convert serialized data to JSON

### Should Do
1. Add comprehensive indexes
2. Standardize NULL handling
3. Add Laravel timestamps (created_at, updated_at)
4. Remove denormalized fields

### Nice to Have
1. Add soft deletes
2. Add audit trail
3. Optimize field sizes
4. Add validation constraints

---

**Document Version:** 1.0  
**Created:** 2025-11-22  
**Source:** Real production SQL dump (33MB)  
**Status:** Analysis Complete

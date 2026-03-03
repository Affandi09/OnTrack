# IT Helpdesk System - Migration Documentation

## Project Overview

**Current Stack:**
- PHP 7.x (legacy)
- MySQL Database
- Medoo Query Builder
- Vanilla JavaScript + jQuery
- Server-side rendering (PHP templates)

**Application Type:** IT Helpdesk & Asset Management System

**Deployment:** Production (Active users)

---

## Core Features

### 1. **Asset Management**
- Asset tracking with categories, manufacturers, models
- Asset assignment to users/clients
- Custom fields support
- QR code generation and scanning
- Warranty tracking
- Purchase date and supplier management
- Location tracking

### 2. **License Management**
- Software license tracking
- License-to-asset assignment
- Seat management
- License categories
- Serial number encryption
- Custom fields support

### 3. **Ticketing System**
- Multi-department support
- Email-to-ticket conversion
- Ticket replies and attachments
- Status tracking (Open, In Progress, Answered, Reopened, Closed)
- Priority levels (Low, Normal, High)
- Escalation rules (time-based and condition-based)
- Auto-close tickets
- CC support
- Public ticket submission form
- reCAPTCHA integration

### 4. **Project Management**
- Project tracking
- Issue/task management
- Milestones
- Progress tracking (manual or auto-calculated from issues)
- Staff assignment
- Time logging
- Comments system
- File attachments

### 5. **Client Management**
- Multi-client support
- Client notes
- Staff assignment to clients
- Asset/license tag prefixes per client
- Contact management

### 6. **User & Staff Management**
- Role-based permissions
- User types: Admin and User
- LDAP/AD authentication support
- Session management
- Password reset functionality
- Multi-language support per user
- Theme customization
- Avatar support (Gravatar + custom upload)

### 7. **Monitoring System**
- Host monitoring
- Custom checks per host
- Email and SMS alerts
- Status tracking
- Alert notifications to assigned staff

### 8. **Knowledge Base**
- Categories
- Articles with rich content
- Search functionality

### 9. **Reporting**
- Work hours by month
- Assets by category
- Tickets by department
- System logs
- Email logs
- SMS logs

### 10. **API**
- RESTful API
- API key authentication
- Role-based API permissions
- Support for all major resources
- Methods: GET, ADD, EDIT, DELETE, ATTACH, DETACH

---

## Technical Architecture

### Authentication Flow
1. **Internal Authentication:** SHA1 password hashing (needs upgrade)
2. **LDAP Authentication:** Optional LDAP/AD integration
3. **Session Management:** PHP sessions with session_id stored in database
4. **API Authentication:** API key with role-based permissions

### Authorization
- Role-based permission system
- Permissions stored as serialized array in roles table
- Permission check on every action
- Client-based data isolation (multi-tenancy)

### File Management
- File uploads stored in `/uploads` directory
- Files linked to: clients, projects, assets, ticket replies
- File naming: `{id}-{original_filename}`
- Supported: Multiple file uploads

### Notification System
- Email notifications (PHPMailer with SMTP)
- SMS notifications (SMSGlobal, Clickatell)
- FCM push notifications (Firebase Cloud Messaging)
- Template-based notifications with variable replacement
- Notification templates stored in database

### Data Encryption
- License serial numbers encrypted (AES-256-CBC)
- Encryption keys stored in config.php
- Functions: `encrypt_decrypt()`

### Internationalization (i18n)
- GNU gettext (.mo files)
- Language files in `/lang` directory
- Override support in `/lang/override`
- Per-user language preference
- Functions: `__()`, `_e()`, `_x()`

### Cron Jobs
- Email-to-ticket processing
- Escalation rules processing
- Auto-close tickets
- Monitoring checks
- Located in `/crons` directory

---

## Database Structure (Inferred from Code)

### Core Tables

**people**
- id, name, type (admin/user), roleid, clientid, email, ldap_user
- title, mobile, password, theme, sidebar, layout
- notes, signature, sessionid, resetkey, avatar
- autorefresh, lang, ticketsnotification, fcmtoken

**clients**
- id, name, asset_tag_prefix, license_tag_prefix, notes

**assets**
- id, categoryid, adminid, clientid, userid
- manufacturerid, modelid, supplierid, statusid, locationid
- purchase_date, warranty_months, tag, name, serial
- notes, customfields (serialized), qrvalue, peoplename

**licenses**
- id, clientid, statusid, categoryid, supplierid
- seats, tag, name, serial (encrypted), notes
- customfields (serialized), qrvalue

**tickets**
- id, ticket (random 6-digit), departmentid, clientid, userid, adminid
- assetid, projectid, email, subject, status, priority
- timestamp, notes, ccs (serialized), timespent
- respondtime, inprogresstime, closetime

**tickets_replies**
- id, ticketid, peopleid, message, timestamp

**tickets_rules** (Escalation Rules)
- id, ticketid, executed, name
- cond_status, cond_priority, cond_timeelapsed, cond_datetime
- act_status, act_priority, act_assignto, act_notifyadmins, act_addreply, reply

**projects**
- id, clientid, tag, name, notes, description
- startdate, deadline, progress

**issues**
- id, projectid, milestoneid, adminid, clientid
- tag, name, description, status, priority, timestamp

**milestones**
- id, projectid, name, description, releasedate, released

**comments**
- id, issueid, peopleid, comment, timestamp

**files**
- id, clientid, projectid, assetid, ticketreplyid
- name, file (filename)

**timelog**
- id, projectid, issueid, clientid, peopleid
- date, start, end, description

**hosts** (Monitoring)
- id, clientid, name, ip, notes

**checks** (Monitoring)
- id, hostid, type, port, keyword, status, laststatus, lastcheck

**hosts_people** (Monitoring Alerts)
- id, hostid, peopleid, email, sms

### Lookup/Attribute Tables

- assetcategories (id, name, color)
- licensecategories (id, name, color)
- statuslabels (id, name, color, type)
- manufacturers (id, name)
- models (id, name)
- suppliers (id, name)
- locations (id, clientid, name)
- tickets_departments (id, name, email)

### Relationship Tables

- licenses_assets (id, licenseid, assetid)
- clients_admins (id, clientid, adminid)
- projects_admins (id, projectid, adminid)
- hosts_people (id, hostid, peopleid, email, sms)

### System Tables

- roles (id, name, perms (serialized))
- config (name, value)
- statuscodes (code, message)
- notificationtemplates (id, name, subject, message, sms)
- api_keys (id, name, secretkey, roleid)
- systemlog (id, peopleid, ipaddress, description, timestamp)
- emaillog (id, peopleid, clientid, to, subject, message, timestamp)
- smslog (id, peopleid, clientid, mobile, sms, timestamp)

### Custom Fields

- assets_customfields (id, name, type)
- licenses_customfields (id, name, type)

### Knowledge Base

- kb_categories (id, name, description)
- kb_articles (id, categoryid, title, content, timestamp)

### QR Codes

- qrcodes (id, batchid, value, type, attached)

### Predefined Replies

- predefined_replies (id, name, reply)

### Contacts

- contacts (id, clientid, name, email, mobile, notes)

### Credentials

- credentials (id, clientid, name, username, password (encrypted), notes)

---

## Key Business Logic

### Ticket Workflow
1. Ticket created (via form, email, or API)
2. Auto-assign to admin if user has assigned asset
3. Email notification to user (optional)
4. Email notification to all admins with ticketsnotification=1
5. Replies can be added by users or admins
6. Status changes: Open → In Progress → Answered → Closed
7. Can be Reopened from any status
8. Escalation rules can auto-change status/priority/assignment
9. Auto-close after X hours of inactivity (configurable)

### Asset Assignment
- Asset can be assigned to one user
- Asset can have multiple licenses assigned
- Asset belongs to one client
- Asset has one admin (responsible staff)
- Asset has one location

### License Management
- License has X seats
- License can be assigned to multiple assets (up to seat limit)
- License serial is encrypted in database
- License belongs to one client

### Project Progress
- Can be manual (slider 0-100%)
- Can be auto-calculated from issues (done/total)
- Progress = -1 means auto-calculate

### Permission System
- Permissions stored as array in role
- Example permissions: addAsset, editAsset, deleteAsset, viewAsset
- Checked via `isAuthorized()` function
- API has separate permission check via `isAuthorizedApi()`

### Multi-tenancy
- Data isolated by clientid
- Admins can be assigned to specific clients
- Users belong to one client
- Assets, licenses, projects belong to clients
- `isOwner()` function checks if user can access client data

---

## Critical Dependencies

### PHP Libraries (Composer)
- medoo/medoo - Database query builder
- phpmailer/phpmailer - Email sending
- gettext-reader - Internationalization
- (Check composer.json for complete list)

### JavaScript Libraries
- jQuery
- Bootstrap
- DataTables
- Select2
- DatePicker
- Chart.js (for reports)
- (Check template files for complete list)

---

## Security Considerations

### Current Issues
1. **SHA1 Password Hashing** - Weak, needs upgrade to bcrypt/argon2
2. **Serialized Data** - PHP serialize() used for arrays (permissions, custom fields)
3. **SQL Injection** - Mitigated by Medoo, but needs verification
4. **XSS** - Need to verify output escaping
5. **CSRF** - No visible CSRF protection
6. **Session Fixation** - Need to verify session regeneration

### Encryption
- AES-256-CBC for sensitive data (license serials, credentials)
- Keys stored in config.php (should be in environment variables)

---

## Migration Challenges

### High Complexity
1. **Serialized Data** - Need to migrate to JSON or relational structure
2. **Custom Fields** - Dynamic fields stored as serialized arrays
3. **Permission System** - Complex role-based permissions
4. **Multi-tenancy** - Client isolation logic throughout
5. **Email-to-Ticket** - Complex parsing and matching logic
6. **Escalation Rules** - Time-based and condition-based automation

### Medium Complexity
1. **File Uploads** - Need to migrate file storage strategy
2. **Notifications** - Multiple channels (email, SMS, FCM)
3. **LDAP Integration** - Need to maintain compatibility
4. **API** - Need to maintain backward compatibility
5. **Internationalization** - Migrate from gettext to modern i18n

### Low Complexity
1. **CRUD Operations** - Straightforward migration
2. **Authentication** - Standard session-based auth
3. **Reporting** - Simple aggregation queries

---

## Recommended Migration Strategy

### Phase 1: Foundation (Week 1-2)
- Setup Laravel project
- Database migration (schema)
- Authentication system
- Basic CRUD for one module (Locations)

### Phase 2: Core Modules (Week 3-8)
- Clients
- Assets
- Licenses
- Users & Staff
- Roles & Permissions

### Phase 3: Complex Features (Week 9-12)
- Tickets (with replies, escalation)
- Projects (with issues, milestones)
- Monitoring
- Knowledge Base

### Phase 4: Integration (Week 13-14)
- API (maintain compatibility)
- Notifications (email, SMS, FCM)
- File uploads
- LDAP integration

### Phase 5: Polish (Week 15-16)
- Frontend (Livewire components)
- Testing
- Data migration scripts
- Documentation

---

## Success Metrics

- [ ] All features working in new system
- [ ] API backward compatible
- [ ] Zero data loss during migration
- [ ] Performance equal or better
- [ ] User training completed
- [ ] Rollback plan tested

---

## Next Steps

1. Review this documentation
2. Decide on Laravel + Livewire approach
3. Setup development environment
4. Create database migration scripts
5. Start with Phase 1 (Foundation)

---

**Document Version:** 1.0  
**Last Updated:** 2025-11-22  
**Author:** Migration Analysis

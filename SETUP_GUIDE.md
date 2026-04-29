# SignED SPED System - Setup Guide

Complete setup guide for fresh installation on a new PC/server.

---

## 📋 Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Apache/Nginx**: Web server with mod_rewrite enabled
- **Composer**: (Optional) For dependency management
- **SMTP Server**: For email notifications (Gmail, Outlook, etc.)

---

## 🚀 Quick Setup (3 Steps)

### Step 1: Database Setup

1. Open **phpMyAdmin**
2. Click "SQL" tab
3. Open file: **`DATABASE_MASTER_SETUP.sql`**
4. Copy ALL code
5. Paste and click "Go"
6. Done! ✅

**What it creates**:
- Database: `signed_system`
- 15 tables (users, enrollments, learners, assessments, IEPs, etc.)
- Default admin account
- System settings

### Step 2: Configure Application

1. Open `config/config.php`
2. Update database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'signed_system');
```

3. Update SMTP credentials (for email):
```php
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
```

### Step 3: Login & Configure

1. Go to: `http://localhost/SignED_/public/auth/login`
2. Login with default admin:
   - **Email**: `admin@signed.local`
   - **Password**: `password`
3. **IMPORTANT**: Change password immediately!
4. Go to **Admin > Settings** and configure:
   - Session timeout
   - SMTP settings
   - System name

**Done! System is ready to use!** ✅

---

## 📊 System Features

### ✅ Completed Features (Steps 1-4)

#### 1. User Management
- Registration with email verification
- Role-based access (5 roles: teacher, parent, sped_teacher, guidance, principal)
- Admin account (created manually)
- Learner accounts (auto-created on enrollment approval)

#### 2. Enrollment Process (Steps 1-2)
- BEEF form submission
- Document upload (PSA Birth Certificate)
- SPED verification
- Approval/Rejection workflow

#### 3. LRN Generation (Step 3)
- Auto-generates LRN when enrollment approved
- Format: YYYYMM + 6 random digits
- Creates learner account (LRN as username, "default123" as password)
- Sends email to parent with credentials

#### 4. Parent Assessment Form (Step 4)
- Section 1: Auto-filled from BEEF (read-only)
- Section 2: Parent fills education history (REQUIRED)
- Section 3: Additional information (OPTIONAL)
- Draft saving functionality
- SPED review page

#### 5. Admin Features
- **System Settings**: Session timeout, email settings, system name
- **Announcements**: Create, edit, delete with priority levels
- **User Management**: View, edit, delete users
- **Audit Logs**: Track all system actions
- **Dashboard Statistics**: Overview of system activity

#### 6. Notification System
- Automatic notifications for announcements
- Email notifications for enrollment, LRN generation
- Dashboard announcements (priority-based display)
- Target audience filtering

### ⏳ Remaining Features (Steps 5-7)

- **Step 5**: IEP Draft Creation
- **Step 6**: Meeting Scheduling with Calendar
- **Step 7**: IEP Finalization (Guidance review, Principal approval)

---

## 👥 User Roles & Access

| Role | Description | Access |
|------|-------------|--------|
| **Admin** | System administrator | Full system access, settings, user management |
| **SPED Teacher** | Special education teacher | Enrollment verification, assessments, IEP creation |
| **Guidance** | Guidance counselor | Meeting scheduling, IEP review |
| **Principal** | School principal | IEP approval, final decisions |
| **Teacher** | Regular teacher | View learner progress, materials |
| **Parent** | Parent/Guardian | Enrollment, assessment form, view IEP |
| **Learner** | Student | View materials, submit work |

---

## 🔐 Default Accounts

### Admin Account
- **Email**: `admin@signed.local`
- **Password**: `password`
- **⚠️ CHANGE PASSWORD IMMEDIATELY!**

### Creating Additional Admins
1. User registers normally
2. Verifies email
3. Chooses any role
4. Admin promotes user to admin role in **Admin > Users**

---

## 📧 Email Configuration

### Gmail Setup

1. Enable 2-Factor Authentication on your Gmail account
2. Generate App Password:
   - Go to: https://myaccount.google.com/apppasswords
   - Select "Mail" and "Other (Custom name)"
   - Copy the 16-character password
3. Update `config/config.php`:
```php
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-16-char-app-password');
```
4. Update **Admin > Settings**:
   - SMTP Host: `smtp.gmail.com`
   - SMTP Port: `587`
   - From Email: `your-email@gmail.com`

### Other Email Providers

**Outlook/Hotmail**:
- SMTP Host: `smtp-mail.outlook.com`
- SMTP Port: `587`

**Yahoo**:
- SMTP Host: `smtp.mail.yahoo.com`
- SMTP Port: `587`

---

## 📁 File Structure

```
SignED_/
├── app/
│   ├── controllers/     # Application controllers
│   ├── models/          # Database models
│   ├── views/           # View templates
│   ├── helpers/         # Helper classes (Mailer, etc.)
│   └── traits/          # Reusable traits
├── config/
│   ├── config.php       # Main configuration
│   ├── database.php     # Database connection
│   └── encryption.key   # Encryption key
├── core/
│   ├── App.php          # Application core
│   ├── Controller.php   # Base controller
│   └── Model.php        # Base model
├── public/
│   ├── index.php        # Entry point
│   └── assets/          # CSS, JS, images
├── storage/             # File uploads
├── logs/                # Application logs
├── cache/               # Cache files
├── DATABASE_MASTER_SETUP.sql  # Master database file
└── README.md            # This file
```

---

## 🔧 Troubleshooting

### Database Connection Error
**Problem**: "Could not connect to database"
**Solution**: 
1. Check `config/config.php` credentials
2. Verify MySQL is running
3. Ensure database `signed_system` exists

### Email Not Sending
**Problem**: OTP or notifications not received
**Solution**:
1. Check SMTP credentials in `config/config.php`
2. Verify SMTP settings in **Admin > Settings**
3. Check if Gmail App Password is correct
4. Verify firewall allows SMTP port 587

### Registration Error
**Problem**: "Column 'role' cannot be null"
**Solution**: Run `DATABASE_MASTER_SETUP.sql` again (role column should be nullable)

### Announcements Not Showing
**Problem**: No announcements on dashboard
**Solution**:
1. Create announcement as admin
2. Set target audience correctly
3. Ensure announcement is active
4. Check expiration date

### Login Issues
**Problem**: Can't login with admin account
**Solution**:
1. Verify email: `admin@signed.local`
2. Verify password: `password`
3. Check if admin account exists in database:
```sql
SELECT * FROM users WHERE role = 'admin';
```

---

## 📊 Database Tables

| Table | Description |
|-------|-------------|
| `users` | User accounts (all roles) |
| `enrollments` | Enrollment applications |
| `learners` | Learner records with LRN |
| `lrn_generation_log` | LRN generation history |
| `assessments` | Parent assessment forms |
| `ieps` | Individualized Education Plans |
| `iep_meetings` | IEP meeting schedules |
| `learning_materials` | Learning materials for learners |
| `document_store` | Document uploads |
| `system_settings` | System configuration |
| `announcements` | System announcements |
| `announcement_reads` | Track read status |
| `notifications` | In-app notifications |
| `audit_logs` | System activity logs |
| `error_logs` | Error tracking |

---

## 🎯 Workflow Overview

### Enrollment Workflow (Steps 1-4)

1. **Parent Registration**
   - Parent registers account
   - Verifies email with OTP
   - Chooses "Parent" role

2. **BEEF Form Submission** (Step 1)
   - Parent fills Basic Education Enrollment Form
   - Provides learner information
   - Status: `pending_verification`

3. **Document Upload** (Step 2)
   - Parent uploads PSA Birth Certificate
   - Document stored securely
   - Status: `pending_verification`

4. **SPED Verification**
   - SPED Teacher reviews enrollment
   - Verifies documents
   - Approves or rejects
   - Status: `verified` or `rejected`

5. **LRN Generation** (Step 3)
   - System auto-generates LRN
   - Creates learner account
   - Sends credentials to parent
   - Status: `approved`

6. **Parent Assessment** (Step 4)
   - Parent fills assessment form
   - Section 1: Auto-filled (read-only)
   - Section 2: Education history (required)
   - Section 3: Additional info (optional)
   - Status: `submitted`

7. **SPED Review**
   - SPED Teacher reviews assessment
   - Marks as reviewed
   - Status: `reviewed`

### IEP Workflow (Steps 5-7) - Coming Soon

8. **IEP Draft Creation** (Step 5)
9. **Meeting Scheduling** (Step 6)
10. **IEP Finalization** (Step 7)

---

## 🔒 Security Features

- ✅ Password hashing (bcrypt)
- ✅ Email verification (OTP)
- ✅ Session management with timeout
- ✅ Login attempt limiting (5 attempts = 30 min lockout)
- ✅ Role-based access control
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection (input sanitization)
- ✅ CSRF protection (tokens)
- ✅ Audit logging (all actions tracked)
- ✅ Secure file uploads (validation, storage)

---

## 📞 Support

For issues or questions:
1. Check this guide first
2. Review error logs in `logs/` folder
3. Check audit logs in **Admin > Logs**
4. Contact system administrator

---

## 📝 Version History

### v1.0 (Current)
- ✅ User management
- ✅ Enrollment process (Steps 1-2)
- ✅ LRN generation (Step 3)
- ✅ Parent assessment (Step 4)
- ✅ Admin features
- ✅ Notification system
- ⏳ IEP workflow (Steps 5-7) - In Progress

---

## 🎉 Quick Start Checklist

- [ ] Run `DATABASE_MASTER_SETUP.sql` in phpMyAdmin
- [ ] Update `config/config.php` with database credentials
- [ ] Update `config/config.php` with SMTP credentials
- [ ] Login as admin: `admin@signed.local` / `password`
- [ ] Change admin password
- [ ] Configure SMTP settings in **Admin > Settings**
- [ ] Test registration with new user
- [ ] Test enrollment process
- [ ] Create test announcement
- [ ] System ready to use! ✅

---

**Last Updated**: April 29, 2026
**System**: SignED SPED Management System
**Database**: DATABASE_MASTER_SETUP.sql

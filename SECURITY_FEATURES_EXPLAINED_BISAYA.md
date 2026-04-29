# SECURITY FEATURES - EXPLAINED IN BISAYA

## 1. AUTHENTICATION MODULE (Pag-verify sa User)

### 1.1 Registration/Login (Pag-register ug Pag-login)

**Unsa ni?**
- Mao ni ang sistema nga mag-verify kung kinsa ka ug kung naa ka'y access sa system.

**Unsaon ni paggamit?**
1. **Registration** - Mag-register ang user gamit ang email ug password
   - Mu-send og OTP (6-digit code) sa email
   - Kinahanglan i-verify ang email within 10 minutes
   - Kung dili ma-verify, dili ka maka-login

2. **Login** - Mag-login gamit ang email ug password
   - I-check kung tama ang password
   - Kung sayop 5 times, ma-lock ang account for 30 minutes
   - Kung tama, ma-redirect ka based sa imong role

**Example:**
```
Parent mu-register:
1. Mu-fill up og form (name, email, password)
2. Mu-receive og OTP sa email (example: 123456)
3. I-enter ang OTP sa verification page
4. Kung tama, verified na ang account
5. Pwede na mu-login
```

**Asa makita?**
- Registration page: `localhost/SignED_/auth/register`
- Login page: `localhost/SignED_/auth/login`
- Code: `app/controllers/AuthController.php`

---

### 1.2 Password Policy (Mga Rules sa Password)

**Unsa ni?**
- Mga requirements para sa password para dili sayon ma-hack.

**Mga Requirements:**
1. ✅ **Minimum 8 characters** - Kinahanglan 8 ka letters o numbers
2. ✅ **Uppercase letter** - Kinahanglan naa'y dakong letra (A-Z)
3. ✅ **Lowercase letter** - Kinahanglan naa'y gamay nga letra (a-z)
4. ✅ **Number** - Kinahanglan naa'y numero (0-9)
5. ✅ **Special character** - Kinahanglan naa'y special character (!@#$%^&*)

**Example:**
```
❌ BAD: "password" - wala'y uppercase, number, special char
❌ BAD: "Pass123" - wala'y special char, kulang pa og 1 char
✅ GOOD: "Pass123!" - naa na tanan requirements
✅ GOOD: "MyP@ssw0rd" - naa na tanan requirements
```

**Asa makita?**
- Registration page: Below sa password field
- Code: `app/controllers/AuthController.php` line 435

**Unsaon pag-test?**
1. Go to registration page
2. Try weak password like "test123"
3. Dili ka maka-register, mu-show og error
4. Try strong password like "Test123!"
5. Pwede na ka maka-register

---

### 1.3 Secure Password Storage (Secure nga Pag-store sa Password)

**Unsa ni?**
- Ang password dili i-store as plain text. Gi-hash (gi-convert to random string) para dili mabasa.

**Unsaon ni?**
- Gamit ang **bcrypt algorithm** (very secure)
- Automatic salt generation (random data added to password)
- One-way hashing (dili ma-reverse, dili makuha ang original password)

**Example:**
```
Original password: "MyP@ssw0rd"
Stored in database: "$2y$10$abcdefghijklmnopqrstuvwxyz1234567890"

Kung ma-hack ang database, dili nila makuha ang original password!
```

**Asa makita?**
- Database: `users` table, `password` column
- Code: `app/controllers/AuthController.php` line 119

**Unsaon pag-verify?**
1. Open phpMyAdmin
2. Go to `users` table
3. Check `password` column
4. Makita nimo nga random string, dili ang actual password

---

## 2. AUTHORIZATION MODULE (Pag-control sa Access)

### 2.1 RBAC - Role-Based Access Control (Access based sa Role)

**Unsa ni?**
- Ang access sa system depende sa imong role (Admin, Parent, SPED Teacher, etc.)
- Dili pwede ang Parent mu-access sa Admin features

**Mga Roles:**
1. **Admin** - Full access sa tanan
2. **SPED Teacher** - Verify enrollments, manage IEPs, assessments
3. **Guidance** - IEP meetings, student records
4. **Principal** - IEP approvals, reports
5. **Parent** - Enroll child, submit assessment
6. **Learner** - View materials, submit work
7. **Teacher** - General education teacher

**Example:**
```
Parent tries to access: /enrollment/verify (SPED Teacher only)
Result: Access denied! Redirect to parent dashboard

SPED Teacher tries to access: /enrollment/verify
Result: Success! Can view enrollments
```

**Asa makita?**
- Code: `app/models/SecurityManager.php` line 234
- Every controller has role checks

**Unsaon pag-test?**
1. Login as Parent
2. Try to access: `localhost/SignED_/enrollment/verify`
3. Dili ka maka-access, mu-redirect or error
4. Login as SPED Teacher
5. Try same URL
6. Pwede na ka maka-access

---

### 2.2 API/Controller Permission Checks (Pag-check sa Permission)

**Unsa ni?**
- Before mu-run ang function, i-check una kung naa ka'y permission.

**Example:**
```php
// EnrollmentController.php
public function submit() {
    $this->requireParent(); // Check if user is parent
    
    // Kung dili parent, mu-stop diri
    // Kung parent, continue sa code
}
```

**Mga Permission Checks:**
- `requireParent()` - Kinahanglan parent ka
- `requireSpedStaff()` - Kinahanglan SPED staff ka
- `requireAdmin()` - Kinahanglan admin ka

**Asa makita?**
- All controllers (EnrollmentController, AssessmentController, etc.)
- Code: `app/traits/SecurityValidation.php`

---

## 3. SECURE DATA STORAGE (Secure nga Pag-store sa Data)

### 3.1 Encrypted Document Storage (Encrypted nga Documents)

**Unsa ni?**
- Ang documents (PSA, PWD ID, Medical Records) gi-encrypt before i-store.
- Kung ma-hack ang server, dili nila mabasa ang documents.

**Unsaon ni?**
- Document gi-encrypt gamit encryption key
- Encryption key gi-store separately
- Ang encrypted file gi-store sa `/app/storage/documents/`

**Example:**
```
Original file: PSA_Birth_Certificate.pdf
Encrypted file: a1b2c3d4e5f6g7h8i9j0.enc

Kung ma-hack, makuha nila ang .enc file pero dili nila mabasa!
```

**Asa makita?**
- Code: `app/models/DocumentStore.php`
- Storage: `/app/storage/documents/`

---

### 3.2 Hashed Passwords (Gi-hash nga Passwords)

**Unsa ni?**
- Same sa #1.3 - Passwords gi-hash using bcrypt

---

### 3.3 Encrypted Sensitive Fields (Encrypted nga Sensitive Data)

**Unsa ni?**
- Ang sensitive data sa database gi-encrypt.
- Example: Document files, personal information

**Asa makita?**
- Database: `document_store` table
- Code: `app/models/DocumentStore.php`

---

## 4. LOGGING AND MONITORING (Pag-track sa Activities)

### 4.1 Login Attempt Logs (Pag-track sa Login Attempts)

**Unsa ni?**
- Gi-track ang tanan login attempts (success or failed)
- Kung daghan og failed attempts, ma-lock ang account

**Unsa ang gi-track?**
- Email address
- IP address
- Timestamp
- Success or failed

**Example:**
```
User: parent@example.com
Failed attempts: 3
Last attempt: 2026-04-29 12:30:00
Status: Not locked (need 5 attempts to lock)

After 5 failed attempts:
Status: LOCKED for 30 minutes
```

**Asa makita?**
- Storage: `/cache/login_attempts_[hash].txt`
- Code: `app/controllers/AuthController.php` line 463

**Unsaon pag-check?**
1. Go to `/cache/` folder
2. Look for files like `login_attempts_*.txt`
3. Open file, makita nimo ang attempts count

---

### 4.2 Admin Activity Logs (Pag-track sa Admin Activities)

**Unsa ni?**
- Gi-track ang TANAN activities sa system
- Useful for audit ug investigation

**Unsa ang gi-track?**
1. **Login/Logout** - Kinsa mu-login, kanus-a
2. **Document Access** - Kinsa nag-view/download og documents
3. **Status Changes** - Kinsa nag-approve/reject og enrollment
4. **Role Changes** - Kinsa nag-change og user role
5. **Approval Actions** - Kinsa nag-approve og IEP
6. **Meeting Scheduling** - Kinsa nag-schedule og meeting
7. **Email Notifications** - Unsa nga emails na-send
8. **System Errors** - Unsa nga errors nahitabo

**Example:**
```
Log Entry:
- User: sped_teacher@example.com
- Action: Approved enrollment
- Entity: Enrollment ID 123
- Timestamp: 2026-04-29 14:30:00
- IP Address: 192.168.1.100
```

**Asa makita?**
- Database: `audit_logs` table
- Code: `app/models/AuditLog.php`
- Admin view: `localhost/SignED_/admin/logs`

**Unsaon pag-check?**
1. Login as Admin
2. Go to: `localhost/SignED_/admin/logs`
3. Makita nimo ang tanan activities
4. Or open phpMyAdmin, check `audit_logs` table

---

## 5. DLP FEATURES (Data Loss Prevention)

### 5.1 Data Classification (Pag-classify sa Data)

**Unsa ni?**
- Ang documents gi-classify based sa sensitivity level
- Different restrictions based sa classification

**Classification Levels:**
1. **Public** - Pwede sa tanan, wala'y restrictions
2. **Internal** - Internal use only, basic restrictions
3. **Confidential** - Restricted access, watermark required
4. **Restricted** - Highly sensitive, screenshot blocked

**Document Classification:**
```
Enrollment documents → Confidential
Assessment records → Restricted
IEP documents → Restricted
Learning materials → Internal
Student submissions → Confidential
Meeting records → Restricted
```

**Restrictions per Level:**

**Restricted Documents:**
- ✅ Watermark required (mu-show og "CONFIDENTIAL" sa document)
- ✅ Screenshot blocked (dili ka maka-screenshot)
- ✅ Copy/paste blocked (dili ka maka-copy og text)
- ✅ Right-click disabled (dili ka maka-right click)
- ✅ Text selection disabled (dili ka maka-select og text)
- ✅ Developer tools disabled (dili ka maka-open og DevTools)

**Confidential Documents:**
- ✅ Watermark required
- ✅ Right-click disabled

**Asa makita?**
- Code: `app/models/SecurityManager.php` line 15
- Database: `document_store` table, `classification` column

**Unsaon pag-test?**
1. Upload enrollment document
2. Check database `document_store` table
3. Makita nimo `classification = 'confidential'`
4. Try to view document
5. Dili ka maka-right click or screenshot

---

### 5.2 Session Timeout (Automatic Logout)

**Unsa ni?**
- Kung wala ka'y activity for 15 minutes, automatic ka ma-logout
- Security feature para kung gibiyaan nimo ang computer

**Configuration:**
- **Timeout:** 15 minutes (900 seconds)
- **Check:** Every page load
- **Action:** Automatic logout ug redirect to login

**Example:**
```
12:00 PM - User logs in
12:10 PM - User views enrollment
12:15 PM - User views assessment
12:30 PM - User tries to view IEP
Result: Session expired! Redirect to login with message
```

**Asa makita?**
- Code: `app/models/SecurityManager.php` line 11, 108
- Code: `app/traits/SecurityValidation.php`

**Unsaon pag-test?**
1. Login to system
2. Wait 15 minutes (or change timeout to 1 minute for testing)
3. Try to click any link
4. Mu-redirect ka to login with "Session expired" message

---

### 5.3 Screenshot Blocking / Export Restriction (Pag-block sa Screenshot)

**Unsa ni?**
- Para sa highly sensitive documents (Restricted classification)
- Dili ka maka-screenshot or copy ang content

**Features:**
1. **Screenshot Blocking**
   - Browser-level restrictions
   - Dili mu-gana ang Print Screen key
   - Dili mu-gana ang screenshot tools

2. **Copy/Paste Blocking**
   - Dili ka maka-copy og text
   - Dili ka maka-paste

3. **Right-Click Disabled**
   - Dili ka maka-right click
   - Dili ka maka-"Save As"

4. **Text Selection Disabled**
   - Dili ka maka-select og text

5. **Developer Tools Disabled**
   - Dili ka maka-open og browser DevTools
   - Prevents bypassing restrictions

**Asa makita?**
- Code: `app/models/SecurityManager.php` line 154
- Applied automatically based sa document classification

**Unsaon pag-test?**
1. Upload IEP document (Restricted classification)
2. View the document
3. Try to:
   - Screenshot (dili mu-gana)
   - Right-click (disabled)
   - Select text (disabled)
   - Copy text (disabled)
   - Open DevTools (F12 disabled)

---

## 6. ADDITIONAL SECURITY FEATURES

### 6.1 CSRF Protection (Cross-Site Request Forgery Protection)

**Unsa ni?**
- Protection against fake form submissions from other websites
- Gamit ang CSRF token (random string) sa each form

**Unsaon ni?**
```
1. User opens form
2. System generates CSRF token (random 64-character string)
3. Token gi-store sa session ug sa form (hidden field)
4. User submits form
5. System checks if token sa form matches token sa session
6. Kung match, proceed. Kung dili, reject.
```

**Example:**
```html
<form method="POST">
    <input type="hidden" name="csrf_token" value="a1b2c3d4e5f6...">
    <!-- other form fields -->
</form>
```

**Asa makita?**
- Code: `app/traits/SecurityValidation.php` line 95
- All forms have CSRF token

---

### 6.2 Rate Limiting (Pag-limit sa Requests)

**Unsa ni?**
- Limit sa number of requests per hour
- Prevents spam ug brute force attacks

**Limits:**
- **Enrollment:** 5 submissions per hour
- **Assessment:** 10 submissions per hour
- **IEP:** 10 submissions per hour
- **Meeting:** 20 submissions per hour
- **File Upload:** 10 uploads per hour per user

**Example:**
```
Parent submits enrollment 5 times in 1 hour
6th attempt: "Too many submissions. Please try again later."
```

**Asa makita?**
- Code: `app/traits/SecurityValidation.php` line 122
- Storage: `/cache/rate_limit_*.txt`

---

### 6.3 SQL Injection Prevention (Pag-prevent sa SQL Injection)

**Unsa ni?**
- Protection against malicious SQL queries
- Gamit ang parameterized queries (prepared statements)

**Example of SQL Injection Attack:**
```sql
-- Attacker tries:
Email: admin@example.com' OR '1'='1
Password: anything

-- Without protection, SQL becomes:
SELECT * FROM users WHERE email = 'admin@example.com' OR '1'='1' AND password = 'anything'
-- This returns all users! (because '1'='1' is always true)

-- With protection (parameterized query):
SELECT * FROM users WHERE email = ? AND password = ?
-- The '?' are replaced safely, SQL injection dili mu-gana
```

**Asa makita?**
- Code: `app/traits/SecurityValidation.php` line 232
- All database queries use prepared statements

---

### 6.4 XSS Prevention (Cross-Site Scripting Prevention)

**Unsa ni?**
- Protection against malicious JavaScript code
- Gamit ang output sanitization

**Example of XSS Attack:**
```html
-- Attacker enters in name field:
<script>alert('Hacked!');</script>

-- Without protection, mu-display as:
<h1>Welcome <script>alert('Hacked!');</script></h1>
-- The script will run!

-- With protection, mu-display as:
<h1>Welcome &lt;script&gt;alert('Hacked!');&lt;/script&gt;</h1>
-- The script is displayed as text, dili mu-run
```

**Asa makita?**
- Code: `app/traits/SecurityValidation.php` line 290
- Applied to all user inputs before display

---

### 6.5 Session Hijacking Prevention (Pag-prevent sa Session Hijacking)

**Unsa ni?**
- Protection against session stealing
- Gamit ang session fingerprinting

**Unsaon ni?**
```
1. User logs in
2. System creates session fingerprint based on:
   - User-Agent (browser info)
   - Accept-Language (language settings)
   - Accept-Encoding (encoding settings)
3. Fingerprint gi-store sa session
4. Every request, i-check if fingerprint matches
5. Kung dili match, session hijacking attempt! Logout user.
```

**Example:**
```
User logs in from Chrome on Windows
Fingerprint: abc123def456

Attacker steals session cookie, tries to use from Firefox on Linux
Fingerprint: xyz789ghi012

System detects mismatch → Logout → Log security event
```

**Asa makita?**
- Code: `app/traits/SecurityValidation.php` line 340

---

### 6.6 Suspicious Activity Detection (Pag-detect sa Suspicious Activity)

**Unsa ni?**
- Automatic detection of suspicious behavior
- Alerts admin if suspicious activity detected

**Checks:**
1. **Rapid Access Attempts**
   - More than 10 document access in 1 minute
   - Action: Rate limit, log event

2. **Access Outside Normal Hours**
   - Access between 10 PM - 6 AM
   - Action: Alert admin, log event

**Example:**
```
User accesses 15 documents in 30 seconds
System detects: Suspicious activity!
Action: Block further access, send alert to admin
```

**Asa makita?**
- Code: `app/models/SecurityManager.php` line 450

---

## SUMMARY TABLE

| Feature | Purpose | Where to Find |
|---------|---------|---------------|
| **Password Policy** | Strong passwords | `AuthController.php:435` |
| **Account Lockout** | Prevent brute force | `AuthController.php:463` |
| **Password Hashing** | Secure storage | `AuthController.php:119` |
| **RBAC** | Role-based access | `SecurityManager.php:234` |
| **Data Classification** | Classify sensitivity | `SecurityManager.php:15` |
| **Session Timeout** | Auto logout | `SecurityManager.php:108` |
| **Screenshot Blocking** | Prevent data leak | `SecurityManager.php:154` |
| **Audit Logs** | Track activities | `AuditLog.php` |
| **CSRF Protection** | Prevent fake forms | `SecurityValidation.php:95` |
| **Rate Limiting** | Prevent spam | `SecurityValidation.php:122` |
| **SQL Injection Prevention** | Secure queries | `SecurityValidation.php:232` |
| **XSS Prevention** | Sanitize output | `SecurityValidation.php:290` |
| **Session Hijacking Prevention** | Secure sessions | `SecurityValidation.php:340` |

---

## HOW TO TEST EACH FEATURE

### 1. Password Policy
```
1. Go to: localhost/SignED_/auth/register
2. Try password: "test" → Should fail
3. Try password: "Test123!" → Should pass
```

### 2. Account Lockout
```
1. Go to: localhost/SignED_/auth/login
2. Enter wrong password 5 times
3. Should see "Account locked" message
4. Wait 30 minutes or clear cache
```

### 3. Session Timeout
```
1. Login to system
2. Wait 15 minutes
3. Try to click any link
4. Should redirect to login with "Session expired"
```

### 4. RBAC
```
1. Login as Parent
2. Try: localhost/SignED_/enrollment/verify
3. Should see "Access denied"
4. Login as SPED Teacher
5. Try same URL → Should work
```

### 5. Audit Logs
```
1. Login as Admin
2. Go to: localhost/SignED_/admin/logs
3. Should see all activities
```

### 6. Data Classification
```
1. Upload enrollment document
2. Open phpMyAdmin
3. Check `document_store` table
4. Should see `classification = 'confidential'`
```

---

## QUESTIONS?

Kung naa pa'y questions about specific security feature, just ask! 😊

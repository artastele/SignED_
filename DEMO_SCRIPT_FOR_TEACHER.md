# DEMO SCRIPT - Security Features Presentation

## 📋 PREPARATION CHECKLIST

Before the demo:
- ✅ Open browser (Chrome recommended)
- ✅ Open phpMyAdmin in another tab
- ✅ Open VS Code with project
- ✅ Have test accounts ready (Parent, SPED Teacher)
- ✅ Have sample documents ready (PSA, PWD ID)

---

## 🎤 PRESENTATION SCRIPT

### INTRODUCTION (1 minute)

**You say:**
> "Good morning/afternoon Ma'am! Today I will demonstrate the security features of the SignED SPED System. The system has **13 major security features** to protect sensitive student data. Let me show you each one."

---

## PART 1: AUTHENTICATION MODULE (5 minutes)

### 1.1 PASSWORD POLICY

**You say:**
> "First, let me show you the password policy. The system requires strong passwords to prevent unauthorized access."

**Demo Steps:**
1. Go to: `localhost/SignED_/auth/register`
2. Fill up the form
3. Try weak password: `test123`

**You say:**
> "Notice that when I enter a weak password like 'test123', the system shows an error. The password must have:
> - At least 8 characters
> - One uppercase letter
> - One lowercase letter
> - One number
> - One special character"

4. Try strong password: `Test123!`

**You say:**
> "Now with a strong password 'Test123!', the system accepts it. This prevents hackers from easily guessing passwords."

**Show in code:**
```
Open: app/controllers/AuthController.php
Scroll to: Line 435
```

**You say:**
> "Here in the code, you can see the password validation function that checks all these requirements."

---

### 1.2 ACCOUNT LOCKOUT

**You say:**
> "Next is the account lockout feature. If someone tries to guess a password multiple times, the account gets locked."

**Demo Steps:**
1. Go to: `localhost/SignED_/auth/login`
2. Enter email: `test@example.com`
3. Enter wrong password 5 times

**You say:**
> "Watch what happens when I enter the wrong password 5 times..."

4. After 5th attempt, show the error message

**You say:**
> "The account is now locked for 30 minutes. This prevents brute force attacks where hackers try thousands of passwords."

**Show in phpMyAdmin:**
```
1. Open phpMyAdmin
2. Go to: File Manager → cache folder
3. Show: login_attempts_*.txt files
```

**You say:**
> "The system tracks failed login attempts in these cache files. After 30 minutes, the counter resets."

---

### 1.3 SECURE PASSWORD STORAGE

**You say:**
> "Now let me show you how passwords are stored securely in the database."

**Demo Steps:**
1. Open phpMyAdmin
2. Go to: `signed_system` database
3. Open: `users` table
4. Show the `password` column

**You say:**
> "Notice that the passwords are not stored as plain text. They are hashed using bcrypt algorithm. Even if someone hacks the database, they cannot read the actual passwords."

**Example:**
```
Original password: Test123!
Stored in database: $2y$10$abcdefghijklmnopqrstuvwxyz...
```

**You say:**
> "This is a one-way hash. It cannot be reversed to get the original password."

---

## PART 2: AUTHORIZATION MODULE (3 minutes)

### 2.1 ROLE-BASED ACCESS CONTROL (RBAC)

**You say:**
> "The system has different user roles with different access levels. Let me demonstrate."

**Demo Steps:**
1. Login as Parent
2. Try to access: `localhost/SignED_/enrollment/verify`

**You say:**
> "I'm logged in as a Parent. Now I'll try to access the enrollment verification page, which is only for SPED teachers."

3. Show the "Access Denied" or redirect

**You say:**
> "See? The system blocks me because I don't have permission. Now let me login as SPED Teacher."

4. Logout
5. Login as SPED Teacher
6. Go to: `localhost/SignED_/enrollment/verify`

**You say:**
> "Now as a SPED Teacher, I can access this page. This is Role-Based Access Control - each role has specific permissions."

**Show the roles:**
```
Open: app/models/SecurityManager.php
Scroll to: Line 234 (checkRoleBasedAccess function)
```

**You say:**
> "Here in the code, you can see the different roles and their permissions:
> - Admin: Full access
> - SPED Teacher: Enrollment, Assessment, IEP
> - Guidance: IEP meetings, Student records
> - Principal: IEP approvals
> - Parent: Enrollment, Assessment submission
> - Learner: Learning materials"

---

## PART 3: DATA ENCRYPTION (5 minutes)

### 3.1 DOCUMENT ENCRYPTION

**You say:**
> "Now let me show you how documents are encrypted. This is very important for protecting sensitive student information."

**IMPORTANT NOTE:** If wala pa'y uploaded files, i-upload una before demo!

**Demo Steps:**

**OPTION A: If naa na'y uploaded files**
1. Open File Explorer
2. Navigate to: `app/storage/documents/`
3. Show the encrypted files

**You say:**
> "Look at these files. They have random names like 'doc_abc123_1234567890.enc'. These are encrypted files. Let me try to open one."

4. Try to open an .enc file with Notepad

**You say:**
> "See? It's just gibberish. The file is encrypted. Even if someone steals these files, they cannot read them without the encryption key."

---

**OPTION B: If wala pa'y files (RECOMMENDED for demo)**
1. Login as Parent
2. Go to enrollment page
3. Upload PSA Birth Certificate

**You say:**
> "I'm uploading a PSA Birth Certificate. The system will automatically encrypt this file before storing it."

4. After upload, open File Explorer
5. Navigate to: `app/storage/documents/`
6. Show the newly created encrypted file

**You say:**
> "See? The file was just uploaded, and it's already encrypted. The filename is random like 'doc_abc123_1234567890.enc'. Let me try to open it."

7. Try to open the .enc file with Notepad

**You say:**
> "Look at this - it's complete gibberish! The file is encrypted using AES-256 encryption, which is military-grade encryption. Even if someone hacks the server and steals this file, they cannot read it without the encryption key."

---

**Show in database:**
```
1. Open phpMyAdmin
2. Go to: document_store table
3. Show the newly uploaded document:
   - original_filename: PSA_Birth_Certificate.pdf
   - encrypted_filename: doc_abc123_1234567890.enc
   - classification: confidential
   - encryption_key_id: key_xyz789_1234567890
```

**You say:**
> "The database stores:
> - The original filename (so we know what it is)
> - The encrypted filename (random name on server)
> - The classification level (confidential)
> - The encryption key ID (used to decrypt the file)
> 
> The encryption key itself is stored separately in a secure location, not in the database."

**Show the encryption key file:**
```
1. Open File Explorer
2. Navigate to: config/encryption.key
3. Try to open with Notepad
```

**You say:**
> "This is the master encryption key. It's a random 32-byte key that's used to encrypt all documents. This file has restricted permissions - only the server can read it."

**Show in code:**
```
Open: app/models/DocumentStore.php
Scroll to: Line 31 (store function)
```

**You say:**
> "Here's the code that encrypts documents:
> 1. It reads the uploaded file
> 2. Generates a unique encryption key ID
> 3. Encrypts the content using AES-256-CBC
> 4. Saves the encrypted file with a random name
> 5. Stores the metadata in the database
> 
> The encryption happens automatically - users don't need to do anything special."

**Show encryption function:**
```
Scroll to: Line 250 (encryptContent function)
```

**You say:**
> "This is the actual encryption function. It uses:
> - AES-256-CBC cipher (very secure)
> - SHA-256 key derivation
> - Random initialization vector (IV)
> - Base64 encoding for storage
> 
> This is the same encryption used by banks and government agencies."

---

## PART 4: DATA CLASSIFICATION (5 minutes)

### 4.1 CLASSIFICATION LEVELS

**You say:**
> "The system classifies documents based on sensitivity level. Different classifications have different restrictions."

**Show in phpMyAdmin:**
```
1. Open: document_store table
2. Show: classification column
```

**You say:**
> "See this 'classification' column? Documents are classified as:
> - Public: No restrictions
> - Internal: Basic restrictions
> - Confidential: Watermark + Right-click disabled
> - Restricted: Screenshot blocked + Copy blocked"

**Show in code:**
```
Open: app/models/SecurityManager.php
Scroll to: Line 15 (classifyDocument function)
```

**You say:**
> "Here's how the system classifies documents:
> - Enrollment documents → Confidential
> - Assessment records → Restricted
> - IEP documents → Restricted
> - Learning materials → Internal"

---

### 4.2 VIEWING RESTRICTIONS

**You say:**
> "Now let me show you the restrictions in action."

**Demo Steps:**
1. Login as SPED Teacher
2. View an enrollment document (Confidential)

**You say:**
> "This is an enrollment document, classified as Confidential. Watch what happens when I try to right-click."

3. Try to right-click

**You say:**
> "Right-click is disabled. But I can still take a screenshot."

4. Take a screenshot (should work)

**You say:**
> "Screenshot works because this is only Confidential level. Now let me show you a Restricted document."

5. View an IEP document (Restricted)

**You say:**
> "This is an IEP document, classified as Restricted. This has maximum security."

6. Try to:
   - Right-click (disabled)
   - Screenshot (blocked)
   - Select text (disabled)
   - Copy text (disabled)

**You say:**
> "Notice that:
> - Right-click is disabled
> - Screenshot is blocked
> - I cannot select or copy text
> - This prevents data leakage"

**Show in code:**
```
Open: app/models/SecurityManager.php
Scroll to: Line 154 (getRestrictions function)
```

**You say:**
> "Here's the code that defines the restrictions for each classification level."

---

## PART 5: LOGGING & MONITORING (3 minutes)

### 5.1 LOGIN ATTEMPT LOGS

**You say:**
> "The system tracks all login attempts for security monitoring."

**Demo Steps:**
1. Open File Explorer
2. Go to: `cache/` folder
3. Show: `login_attempts_*.txt` files

**You say:**
> "These files track failed login attempts. Each file contains:
> - Number of attempts
> - Timestamp of last attempt"

4. Open one file with Notepad

**You say:**
> "See? It shows '3|1714392000' which means 3 attempts at this timestamp."

---

### 5.2 AUDIT LOGS

**You say:**
> "The system also logs all activities for audit purposes."

**Demo Steps:**
1. Open phpMyAdmin
2. Go to: `audit_logs` table
3. Show recent entries

**You say:**
> "This table logs everything:
> - Who logged in
> - Who viewed documents
> - Who approved enrollments
> - Who scheduled meetings
> - Everything is tracked with timestamp and IP address"

4. Show a few sample entries

**You say:**
> "For example, this entry shows that user 'sped_teacher@example.com' approved enrollment ID 123 on April 29, 2026 at 2:30 PM from IP address 192.168.1.100."

**Show in admin panel:**
```
1. Login as Admin
2. Go to: localhost/SignED_/admin/logs
```

**You say:**
> "Administrators can view all logs in this dashboard. They can filter by user, action type, date range, etc."

---

## PART 6: SESSION TIMEOUT (2 minutes)

### 6.1 AUTOMATIC LOGOUT

**You say:**
> "The system has automatic logout after 15 minutes of inactivity. This prevents unauthorized access if someone leaves their computer unattended."

**Demo Steps:**
1. Login to system
2. Show the current time

**You say:**
> "I'm logged in now. If I don't do anything for 15 minutes, the system will automatically log me out."

**For quick demo (optional):**
```
Open: app/models/SecurityManager.php
Line 11: Change $sessionTimeout = 900 to $sessionTimeout = 60 (1 minute)
```

**You say:**
> "For this demo, I've set the timeout to 1 minute. Let's wait..."

3. Wait 1 minute
4. Try to click any link

**You say:**
> "See? The system logged me out and shows 'Session expired' message. This protects the system if someone forgets to logout."

---

## PART 7: ADDITIONAL FEATURES (3 minutes)

### 7.1 CSRF PROTECTION

**You say:**
> "The system has CSRF protection to prevent fake form submissions from other websites."

**Demo Steps:**
1. Open any form (enrollment, assessment)
2. Right-click → Inspect Element
3. Find the hidden CSRF token field

**You say:**
> "See this hidden field? It contains a random token that changes every session. When the form is submitted, the system checks if the token matches. This prevents attackers from submitting fake forms."

---

### 7.2 RATE LIMITING

**You say:**
> "The system limits how many times you can submit forms to prevent spam and abuse."

**Demo Steps:**
1. Show the rate limit configuration

**You say:**
> "The system allows:
> - 5 enrollment submissions per hour
> - 10 assessment submissions per hour
> - 10 file uploads per hour
> 
> If you exceed these limits, you'll see an error message."

---

### 7.3 SQL INJECTION PREVENTION

**You say:**
> "The system prevents SQL injection attacks using parameterized queries."

**Show in code:**
```
Open: app/traits/SecurityValidation.php
Scroll to: Line 232 (executeSecureQuery function)
```

**You say:**
> "All database queries use prepared statements with placeholders. This prevents attackers from injecting malicious SQL code."

**Example:**
```php
// Unsafe (vulnerable to SQL injection):
$sql = "SELECT * FROM users WHERE email = '$email'";

// Safe (using prepared statements):
$sql = "SELECT * FROM users WHERE email = ?";
$stmt->execute([$email]);
```

---

## CONCLUSION (2 minutes)

**You say:**
> "To summarize, the SignED SPED System has comprehensive security features:
> 
> **Authentication:**
> - Strong password policy
> - Account lockout after 5 failed attempts
> - Secure password storage using bcrypt
> 
> **Authorization:**
> - Role-based access control
> - Permission checks on every page
> 
> **Data Protection:**
> - Document encryption
> - Data classification with viewing restrictions
> - Screenshot blocking for sensitive documents
> 
> **Monitoring:**
> - Login attempt tracking
> - Comprehensive audit logs
> - Activity monitoring
> 
> **Additional Security:**
> - Session timeout (15 minutes)
> - CSRF protection
> - Rate limiting
> - SQL injection prevention
> - XSS prevention
> 
> All these features work together to protect sensitive student information and comply with data privacy regulations."

---

## Q&A PREPARATION

### Common Questions & Answers:

**Q: Can we change the session timeout duration?**
A: Yes, it's configurable in `SecurityManager.php` line 11. Currently set to 15 minutes (900 seconds).

**Q: What happens if someone forgets their password?**
A: We can implement a password reset feature that sends a reset link via email.

**Q: Can we see who accessed a specific document?**
A: Yes, all document access is logged in the `audit_logs` table with user ID, timestamp, and IP address.

**Q: What if we need to recover an encrypted document?**
A: The encryption keys are stored in the database. Authorized users can decrypt documents through the system.

**Q: Can we add more classification levels?**
A: Yes, the classification system is flexible. We can add custom levels in `SecurityManager.php`.

**Q: How do we know if there's a security breach?**
A: The system logs suspicious activities and can send alerts to administrators. We can also review audit logs regularly.

---

## TIPS FOR PRESENTATION

1. **Practice the demo beforehand** - Make sure everything works
2. **Have backup accounts ready** - In case one doesn't work
3. **Keep it simple** - Don't go too technical unless asked
4. **Show, don't just tell** - Demonstrate each feature live
5. **Be confident** - You built this, you know it well!
6. **Prepare for questions** - Review the Q&A section
7. **Time management** - Total demo should be 25-30 minutes

---

## BACKUP PLAN

If something doesn't work during demo:
1. **Have screenshots ready** - Show screenshots instead of live demo
2. **Show the code** - Explain how it works in the code
3. **Use phpMyAdmin** - Show database tables as proof
4. **Stay calm** - Explain what should happen even if demo fails

---

## FINAL CHECKLIST

Before presenting:
- ✅ Test all features work
- ✅ Clear browser cache
- ✅ Reset test accounts
- ✅ Have sample documents ready
- ✅ Close unnecessary tabs/windows
- ✅ Increase browser zoom for visibility
- ✅ Turn off notifications
- ✅ Have backup screenshots
- ✅ Print this script
- ✅ Breathe and relax! 😊

---

Good luck sa imong presentation! You got this! 💪

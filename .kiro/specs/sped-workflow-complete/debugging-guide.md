# SPED Workflow Debugging Guide

## Quick Reference: Where to Find Things

### Controllers (app/controllers/)
| Controller | Purpose | Key Methods |
|------------|---------|-------------|
| **EnrollmentController.php** | Steps 1-3 | beef(), upload(), verify(), approve(), reject() |
| **AssessmentController.php** | Step 4 | list(), create(), save() |
| **IepController.php** | Steps 5-7 | list(), create(), meetings(), approve() |
| **LearnerController.php** | Student Records | records(), dashboard(), materials() |
| **ParentController.php** | Parent Dashboard | dashboard(), manageRequirements() |
| **SpedController.php** | SPED Dashboard | dashboard() |

### Models (app/models/)
| Model | Purpose | Key Methods |
|-------|---------|-------------|
| **Enrollment.php** | Enrollment data | createWithBeef(), uploadDocument(), getByStatus() |
| **Learner.php** | Learner records | createFromEnrollment(), getAllEnrolled(), getByStatus() |
| **Assessment.php** | Assessment data | create(), getByLearner() |
| **Iep.php** | IEP data | create(), getByLearner(), getCurrentForLearner() |
| **IepMeeting.php** | Meeting data | schedule(), getUpcoming() |
| **DocumentStore.php** | File storage | store(), retrieve() |
| **User.php** | User accounts | createUser(), getUserById() |

### Views (app/views/)
| View Path | Purpose |
|-----------|---------|
| **enrollment/beef.php** | BEEF form |
| **enrollment/upload.php** | Document upload |
| **enrollment/verify.php** | Verification list |
| **enrollment/view.php** | Review enrollment |
| **learner/records.php** | Student records list |
| **parent/dashboard_bootstrap.php** | Parent dashboard |
| **sped/dashboard_bootstrap.php** | SPED dashboard |

---

## Common Debugging Scenarios

### 1. Enrollment Not Showing Up

**Check:**
```sql
-- Check enrollments table
SELECT * FROM enrollments WHERE parent_id = [PARENT_ID];

-- Check enrollment status
SELECT id, learner_first_name, learner_last_name, status 
FROM enrollments 
ORDER BY created_at DESC;
```

**Common Issues:**
- Status stuck at 'pending_documents'
- Missing PSA document
- Parent ID mismatch

**Fix:**
- Upload PSA document
- Check `hasAllDocuments()` method
- Verify parent_id in session

---

### 2. Learner Not Created After Approval

**Check:**
```sql
-- Check if learner exists
SELECT * FROM learners WHERE parent_id = [PARENT_ID];

-- Check enrollment approval
SELECT id, status, verified_by, verified_at 
FROM enrollments 
WHERE status = 'approved';
```

**Common Issues:**
- `createFromEnrollment()` failed
- Missing BEEF data
- User creation failed

**Fix:**
- Run `fix_approved_enrollments.php` script
- Check error logs
- Verify BEEF data exists in enrollment

---

### 3. Documents Not Uploading

**Check:**
```sql
-- Check enrollment_documents
SELECT * FROM enrollment_documents 
WHERE enrollment_id = [ENROLLMENT_ID];

-- Check document_store
SELECT * FROM document_store 
ORDER BY created_at DESC LIMIT 10;
```

**Common Issues:**
- File size too large
- Invalid file type
- Encryption key missing

**Fix:**
- Check `php.ini` upload limits
- Verify allowed file types
- Check encryption key in `config/encryption.key`

---

### 4. Status Not Updating

**Check:**
```sql
-- Check learner status
SELECT id, first_name, last_name, status 
FROM learners 
ORDER BY updated_at DESC;

-- Check IEP status
SELECT id, learner_id, status 
FROM ieps 
ORDER BY updated_at DESC;
```

**Common Issues:**
- Status transition logic missing
- Database update failed
- Workflow step skipped

**Fix:**
- Check `updateStatus()` methods
- Verify workflow sequence
- Check audit logs for errors

---

### 5. Navigation Not Showing

**Check:**
- File: `app/views/layouts/sidebar.php`
- Look for role-based navigation array
- Verify `$data['role']` is set correctly

**Common Issues:**
- Role not set in session
- Navigation array missing entry
- Current page not matching

**Fix:**
- Check session: `$_SESSION['role']`
- Add navigation entry for role
- Set `$data['current_page']` correctly

---

## Database Quick Checks

### Check All Tables
```sql
-- Show all tables
SHOW TABLES;

-- Count records in each table
SELECT 'users' as table_name, COUNT(*) as count FROM users
UNION ALL
SELECT 'enrollments', COUNT(*) FROM enrollments
UNION ALL
SELECT 'learners', COUNT(*) FROM learners
UNION ALL
SELECT 'assessments', COUNT(*) FROM assessments
UNION ALL
SELECT 'ieps', COUNT(*) FROM ieps
UNION ALL
SELECT 'iep_meetings', COUNT(*) FROM iep_meetings;
```

### Check Workflow Progress
```sql
-- See enrollment pipeline
SELECT 
    status,
    COUNT(*) as count
FROM enrollments
GROUP BY status;

-- See learner statuses
SELECT 
    status,
    COUNT(*) as count
FROM learners
GROUP BY status;

-- See IEP statuses
SELECT 
    status,
    COUNT(*) as count
FROM ieps
GROUP BY status;
```

### Check Specific Learner Journey
```sql
-- Replace [LEARNER_ID] with actual ID
SELECT 
    'Enrollment' as step,
    e.status,
    e.created_at
FROM enrollments e
JOIN learners l ON l.parent_id = e.parent_id
WHERE l.id = [LEARNER_ID]

UNION ALL

SELECT 
    'Learner',
    l.status,
    l.created_at
FROM learners l
WHERE l.id = [LEARNER_ID]

UNION ALL

SELECT 
    'Assessment',
    a.status,
    a.created_at
FROM assessments a
WHERE a.learner_id = [LEARNER_ID]

UNION ALL

SELECT 
    'IEP',
    i.status,
    i.created_at
FROM ieps i
WHERE i.learner_id = [LEARNER_ID]

ORDER BY created_at;
```

---

## Error Log Locations

### PHP Error Log
```
C:\xampp\apache\logs\error.log
```

### Application Error Log
```
logs/error.log
```

### Check Recent Errors
```bash
# Windows PowerShell
Get-Content C:\xampp\apache\logs\error.log -Tail 50

# Or in application
Get-Content logs\error.log -Tail 50
```

---

## Common Error Messages & Solutions

### "Class 'User' not found"
**Cause:** Missing require statement
**Fix:** Add `require_once 'app/models/User.php';` or use conditional require

### "Call to undefined method"
**Cause:** Method doesn't exist in model/controller
**Fix:** Check method name spelling, verify it exists in the class

### "Undefined variable"
**Cause:** Variable not passed to view
**Fix:** Add variable to `$data` array in controller

### "Access denied"
**Cause:** User doesn't have required role
**Fix:** Check `requireSpedStaff()` or role requirements

### "Enrollment ID required"
**Cause:** Missing ID in URL parameter
**Fix:** Check URL has `?id=X` parameter

---

## Testing Checklist

### Step 1: Enrollment
- [ ] Can access BEEF form
- [ ] Can fill out all fields
- [ ] Form validates correctly
- [ ] Enrollment created in database
- [ ] Redirects to upload page

### Step 2: Upload
- [ ] Can access upload page
- [ ] Can upload PSA document
- [ ] Document stored and encrypted
- [ ] Status updates to pending_verification
- [ ] BEEF shows as "Already Submitted"

### Step 3: Verification
- [ ] SPED teacher sees pending enrollments
- [ ] Can view enrollment details
- [ ] Can view documents inline
- [ ] Can approve enrollment
- [ ] Learner record created
- [ ] Email sent to parent

### Step 4: Assessment
- [ ] Can access assessment form
- [ ] Can fill Part A
- [ ] Can fill Part B (optional)
- [ ] Assessment saved
- [ ] Status updates

### Step 5: IEP
- [ ] Can create IEP
- [ ] Links to assessment
- [ ] Can add goals
- [ ] Can add accommodations
- [ ] IEP saved

### Step 6: Meeting
- [ ] Can schedule meeting
- [ ] Participants notified
- [ ] Can confirm attendance
- [ ] Can record notes
- [ ] Status updates

### Step 7: Approval
- [ ] Principal sees pending IEPs
- [ ] Can approve/reject
- [ ] Learner status → active
- [ ] Notifications sent

---

## Quick Fixes Script

### Reset Enrollment Status
```sql
-- Reset to pending_verification (if documents uploaded)
UPDATE enrollments 
SET status = 'pending_verification' 
WHERE id = [ENROLLMENT_ID];
```

### Manually Create Learner
```sql
-- Use the fix script instead:
-- C:\xampp\php\php.exe fix_approved_enrollments.php
```

### Clear Test Data
```sql
-- CAUTION: This deletes data!
DELETE FROM iep_meetings WHERE learner_id IN (SELECT id FROM learners WHERE status = 'enrolled');
DELETE FROM ieps WHERE learner_id IN (SELECT id FROM learners WHERE status = 'enrolled');
DELETE FROM assessments WHERE learner_id IN (SELECT id FROM learners WHERE status = 'enrolled');
DELETE FROM learners WHERE status = 'enrolled';
DELETE FROM enrollment_documents WHERE enrollment_id IN (SELECT id FROM enrollments WHERE status != 'approved');
DELETE FROM enrollments WHERE status != 'approved';
```

---

## Performance Tips

### Slow Queries
- Add indexes on frequently queried columns
- Use `EXPLAIN` to analyze queries
- Limit result sets with pagination

### Large File Uploads
- Increase PHP limits in `php.ini`:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 10M
  max_execution_time = 300
  ```

### Session Issues
- Check session configuration
- Verify session storage location
- Clear old sessions periodically

---

## Contact & Support

### When Stuck
1. Check this debugging guide
2. Check error logs
3. Check database state
4. Review requirements.md
5. Ask for help with specific error message

### Useful Commands
```bash
# Check PHP version
php -v

# Check MySQL connection
mysql -u root -p

# Restart Apache
# XAMPP Control Panel → Apache → Restart

# Clear cache (if implemented)
# Delete files in cache/ folder
```

# DATABASE FIX GUIDE - Step by Step

## PROBLEMA
Nag-run ka multiple times sa database updates, nag-duplicate ang columns.

## SOLUTION
Run ang `database_simple_fix.sql` **ONE QUERY AT A TIME**

---

## STEP-BY-STEP INSTRUCTIONS

### 1. Open phpMyAdmin
- Go to `http://localhost/phpmyadmin`
- Login with your credentials
- Select `signed_system` database from left sidebar

### 2. Go to SQL Tab
- Click "SQL" tab at the top

### 3. Run Queries One by One

#### Query 1: Add assessment_id to ieps
```sql
ALTER TABLE ieps 
ADD COLUMN assessment_id INT NULL AFTER learner_id;
```
- Copy this query
- Paste in SQL tab
- Click "Go"
- **If error "Duplicate column 'assessment_id'"** → ✅ **IGNORE, continue to next**
- **If success** → ✅ **Continue to next**

#### Query 2: Add draft_data to ieps
```sql
ALTER TABLE ieps 
ADD COLUMN draft_data LONGTEXT NULL AFTER assessment_id;
```
- **If error "Duplicate column"** → ✅ **IGNORE, continue**
- **If success** → ✅ **Continue**

#### Query 3: Add meeting_scheduled to ieps
```sql
ALTER TABLE ieps 
ADD COLUMN meeting_scheduled TINYINT(1) DEFAULT 0 AFTER status;
```
- **If error "Duplicate column"** → ✅ **IGNORE, continue**
- **If success** → ✅ **Continue**

#### Query 4: Add school_year to learners
```sql
ALTER TABLE learners 
ADD COLUMN school_year VARCHAR(20) NULL AFTER grade_level;
```
- **If error "Duplicate column"** → ✅ **IGNORE, continue**
- **If success** → ✅ **Continue**

#### Query 5: Add previous_assessment_id to assessments
```sql
ALTER TABLE assessments 
ADD COLUMN previous_assessment_id INT NULL AFTER learner_id;
```
- **If error "Duplicate column"** → ✅ **IGNORE, continue**
- **If success** → ✅ **Continue**

### 4. Create Tables (Run All Together)

Copy ALL the CREATE TABLE queries from `database_simple_fix.sql` (starting from "CREATE TABLE IF NOT EXISTS iep_goals") and run them together. These won't error because they use `IF NOT EXISTS`.

### 5. Verify Results

Run these verification queries:

```sql
-- Check ieps table
SHOW COLUMNS FROM ieps;

-- Check learners table  
SHOW COLUMNS FROM learners;

-- Check assessments table
SHOW COLUMNS FROM assessments;

-- Check IEP tables
SHOW TABLES LIKE 'iep_%';

-- Check enrollment_history
SHOW TABLES LIKE 'enrollment_history';
```

---

## EXPECTED RESULTS

### ieps table should have:
- ✅ `assessment_id` column
- ✅ `draft_data` column
- ✅ `meeting_scheduled` column

### learners table should have:
- ✅ `school_year` column
- ✅ `previous_learner_id` column (already existed)

### assessments table should have:
- ✅ `previous_assessment_id` column

### New tables should exist:
- ✅ `iep_goals`
- ✅ `iep_services`
- ✅ `iep_accommodations`
- ✅ `iep_meeting_attendees`
- ✅ `enrollment_history`

---

## AFTER DATABASE FIX

### Test These Features:

1. **Enrollment** - Parent submits BEEF, SPED approves
2. **Assessment** - Parent fills assessment form
3. **Returning Student** - Parent searches by LRN/name

If all working, then database is fixed! ✅

---

## TROUBLESHOOTING

### Error: "Cannot add foreign key constraint"
**Solution:** The referenced table doesn't exist yet. Make sure `ieps` and `iep_meetings` tables exist first.

### Error: "Table already exists"
**Solution:** This is fine! The table already exists. Continue to next query.

### Error: "Unknown column in field list"
**Solution:** This means the column doesn't exist yet. The ALTER TABLE query should add it.

---

## NEED HELP?

If naa pa'y error after following these steps, send me:
1. The exact error message
2. Which query caused the error
3. Screenshot of the error (if possible)

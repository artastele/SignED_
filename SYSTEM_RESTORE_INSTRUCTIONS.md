# SYSTEM RESTORE INSTRUCTIONS

## PROBLEMA NGA NAHITABO

Based sa context transfer, nag-implement mo og IEP features then naay naguba. Ang problema kay:

1. **Duplicate Column Errors** - Gi-run nimo multiple times ang database updates
2. **Database Structure Confusion** - Nag-overlap ang returning student ug IEP database updates

## ERRORS NGA NA-ENCOUNTER

```
#1060 - Duplicate column name 'assessment_id'
#1060 - Duplicate column name 'school_year'
#1060 - Duplicate column name 'previous_assessment_id'
#1060 - Duplicate column name 'previous_learner_id'
#1054 - Unknown column 'assessment_id' in 'ieps'
```

## SOLUTION: SAFE DATABASE FIX

### STEP 1: Check Current Database Structure

1. Open phpMyAdmin
2. Select `signed_system` database
3. Run the queries in `check_database_structure.sql` one by one
4. Take note of what columns/tables already exist

### STEP 2: Run Simple Fix Script

**IMPORTANT: Use `database_simple_fix.sql` instead of `database_safe_fix.sql`**

1. Open `database_simple_fix.sql` in phpMyAdmin
2. **Run queries ONE BY ONE** (not all at once):
   - Copy first query (ALTER TABLE ieps ADD COLUMN assessment_id...)
   - Paste in SQL tab
   - Click "Go"
   - If you get **"Duplicate column"** error → **IGNORE IT, continue to next query**
   - If successful → Continue to next query
3. Repeat for all ALTER TABLE queries
4. For CREATE TABLE queries, you can run all at once (they use IF NOT EXISTS)
5. Run verification queries at the end to check results

### STEP 3: Verify System Health

After running the fix, test these features:

#### ✅ Enrollment (Should Still Work)
- Parent can submit BEEF form
- Parent can upload documents
- SPED teacher can view enrollments
- SPED teacher can approve/reject

#### ✅ Assessment (Should Still Work)
- Parent can fill assessment form
- Assessment auto-fills learner background
- Education history works
- Part B assessment info works
- SPED teacher can review assessments

#### ✅ Returning Student (Should Still Work)
- Parent can search by LRN or name
- Old data auto-fills BEEF form
- New enrollment creates new record with link to old

#### ⚠️ IEP (Partially Implemented - Not Yet Functional)
- IEP controller exists but views are missing
- Database structure is ready
- Need to create view files before testing

## WHAT WAS IMPLEMENTED (IEP)

### ✅ Database Structure
- `ieps` table updated with `assessment_id`, `draft_data`, `meeting_scheduled`
- `iep_goals` table created
- `iep_services` table created
- `iep_accommodations` table created
- `iep_meeting_attendees` table created

### ✅ Backend Code
- `IepController.php` created with methods:
  - `list()` - List all IEPs
  - `create()` - Create/edit IEP draft
  - `send()` - Send IEP (proceed to meeting)
  - `uploadDraft()` - Upload IEP draft document
  - `scheduleMeeting()` - Schedule IEP meeting
  - `meetings()` - View meetings
  - `confirmAttendance()` - Confirm meeting attendance
  - `recordMeeting()` - Record meeting notes

- `Iep.php` model updated with methods:
  - `createDraft()`, `updateDraft()`
  - `getByLearnerId()`, `getByIdWithLearner()`, `getAllWithLearners()`
  - `updateStatus()`, `markMeetingScheduled()`
  - `saveGoals()`, `saveServices()`, `saveAccommodations()`
  - `getGoals()`, `getServices()`, `getAccommodations()`

### ❌ Missing (Not Yet Created)
- `app/views/iep/list.php` - List all IEPs
- `app/views/iep/create.php` - IEP creation form (based on IEP P2.pdf)
- `app/views/iep/upload_draft.php` - Upload IEP draft document
- `app/views/iep/schedule_meeting.php` - Schedule meeting form
- `app/views/iep/meetings.php` - View all meetings
- `app/views/iep/confirm_attendance.php` - Confirm attendance form
- `app/views/iep/record_meeting.php` - Record meeting notes
- `IepMeeting.php` model - Not created yet
- `IepParticipant.php` model - Not created yet
- Navigation links in sidebar - Not added yet

## NEXT STEPS

### Option 1: Fix Database First, Then Continue IEP
1. Run `database_safe_fix.sql` in phpMyAdmin
2. Test existing features (enrollment, assessment, returning student)
3. If all working, continue with IEP view files

### Option 2: Rollback IEP, Keep Working Features
1. Run `database_safe_fix.sql` to fix structure
2. Remove IEP controller temporarily
3. Focus on other features first
4. Come back to IEP later

## RECOMMENDATION

**I recommend Option 1** - Fix database first, verify existing features work, then continue IEP implementation.

Ang IEP implementation kay almost complete na sa backend side. Kulang na lang ang view files ug models. Once ma-fix ang database, pwede na ta continue.

## FILES TO CHECK AFTER FIX

1. **Enrollment** - `app/controllers/EnrollmentController.php` (line 600-700 - approve method)
2. **Assessment** - `app/controllers/AssessmentController.php` (line 200-300 - submit method)
3. **Learner** - `app/models/Learner.php` (line 300-400 - createFromEnrollment method)

## IMPORTANT REMINDERS

- ❌ **DO NOT run `database_iep_update.sql` again** - It will cause duplicate errors
- ❌ **DO NOT run `database_returning_student_update.sql` again** - It will cause duplicate errors
- ✅ **USE `database_safe_fix.sql` instead** - It checks before adding
- ✅ **Test one feature at a time** - Enrollment → Assessment → Returning Student → IEP

## CONTACT

If naa pa'y problema after running the fix, let me know unsa ang error message ug asa nga part sa system.

# IEP Complete Workflow Implementation

## Overview
Complete IEP workflow from draft creation to final approval with document management, meeting scheduling, and signatures.

## Workflow Steps

### Step 1: Create IEP Draft (SPED Teacher)
- **Status**: `draft`
- **File**: `app/views/iep/create.php` ✅ DONE
- SPED teacher creates IEP draft based on IEP P2.pdf
- Can save multiple times
- Minimum requirements: 1 goal + 1 service

### Step 2: Send IEP & Upload Document (SPED Teacher)
- **Status**: `draft` → `pending_upload` → `pending_meeting`
- **Files**: 
  - `app/views/iep/upload_draft.php` ✅ DONE
  - `IepController::uploadDraft()` ✅ DONE
- SPED clicks "Send IEP"
- Redirects to upload page
- Upload PDF document (max 10MB)
- System sends notifications to:
  - Guidance Counselor (for feedback)
  - Principal (for review)
  - Parent (for information)

### Step 3: Review Draft (Guidance & Principal)
- **Status**: `pending_meeting`
- **Files**: 
  - `app/views/iep/review_draft.php` ⏳ TODO
  - `IepController::reviewDraft()` ✅ DONE
- Guidance and Principal can view IEP draft
- Can add feedback/comments
- Parent can view (read-only)

### Step 4: Schedule Meeting (SPED Teacher)
- **Status**: `pending_meeting` → `meeting_scheduled`
- **Files**: 
  - `app/views/iep/schedule_meeting.php` ✅ DONE (needs update for participants)
  - `IepController::scheduleMeeting()` ⏳ NEEDS UPDATE
- SPED schedules meeting
- Select date, time, location
- Invite participants:
  - Parent/Guardian (required)
  - SPED Teacher (required)
  - Guidance Counselor (required)
  - Principal (required)
  - General Ed Teacher (optional)
  - Other specialists (optional)
- Minimum 3 days notice
- System sends invitations
- Participants confirm/decline

### Step 5: Meeting Management
- **Status**: `meeting_scheduled` → `meeting_completed`
- **Files**: 
  - `app/views/iep/meetings.php` ✅ DONE (needs update)
  - `app/views/iep/confirm_attendance.php` ⏳ TODO
  - `app/views/iep/record_meeting.php` ⏳ TODO
  - `IepController::confirmAttendance()` ⏳ TODO
  - `IepController::recordMeeting()` ⏳ TODO
- Participants confirm attendance
- If parent declines → must reschedule
- On meeting day: record notes and decisions
- Mark as completed

### Step 6: Finalize IEP (SPED Teacher)
- **Status**: `meeting_completed` → `pending_finalization` → `pending_signatures`
- **Files**: 
  - `app/views/iep/finalize.php` ⏳ TODO
  - `IepController::finalize()` ⏳ TODO
- Based on IEP P3.pdf format
- ALL fields required (unlike draft)
- Includes:
  - Meeting decisions
  - Guidance feedback incorporated
  - Step objectives (10 rows)
  - Terminal objectives
  - Priority needs
  - Re-evaluation date
- Generate printable IEP document

### Step 7: Print & Sign (Physical Signatures)
- **Status**: `pending_signatures`
- **Files**: 
  - `app/views/iep/print.php` ⏳ TODO
- Print IEP document
- Collect physical signatures:
  - Parent/Guardian
  - SPED Teacher
  - Guidance Counselor
  - General Ed Teacher (if applicable)
  - Principal
  - ILRC Supervisor
- Scan signed document

### Step 8: Upload Signed Document (SPED Teacher)
- **Status**: `pending_signatures` → `pending_guidance_review`
- **Files**: 
  - `app/views/iep/upload_signed.php` ⏳ TODO
  - `IepController::uploadSigned()` ⏳ TODO
- Upload scanned signed document
- PDF, JPG, or PNG (max 10MB)

### Step 9: Guidance Review (Guidance Counselor)
- **Status**: `pending_guidance_review` → `pending_approval`
- **Files**: 
  - `app/views/iep/guidance_review.php` ⏳ TODO
  - `IepController::guidanceReview()` ⏳ TODO
- Guidance reviews signed document
- Can approve or request revisions
- If approved → proceeds to Principal

### Step 10: Principal Approval
- **Status**: `pending_approval` → `approved` → `active`
- **Files**: 
  - `app/views/iep/approve.php` ✅ EXISTS (needs update)
  - `IepController::approve()` ⏳ TODO
- Principal reviews signed document
- Can approve or reject
- If approved:
  - IEP status → `approved`
  - Learner status → `active`
  - Email notifications sent
- If rejected → back to SPED with reason

## Database Tables

### New Tables Created
1. `iep_meeting_participants` - Meeting invitations and confirmations
2. `iep_feedback` - Feedback from Guidance and Principal
3. `iep_step_objectives` - IEP P3 step objectives (10 rows)
4. `iep_signatures` - Track all required signatures
5. `iep_notifications` - IEP-specific notifications

### Updated Tables
1. `ieps` - Added columns for documents, finalization, guidance review
2. `iep_meetings` - Added columns for notes, decisions, completion
3. `learners` - Added `iep_status` column

## Models Needed

### Existing Models (to update)
- `Iep.php` - Add methods for finalization, documents, signatures
- `IepMeeting.php` - Add methods for participants, completion

### New Models (to create)
- `IepFeedback.php` ⏳ TODO
- `IepParticipant.php` ⏳ TODO
- `IepSignature.php` ⏳ TODO

## Dashboard Updates

### Guidance Dashboard
- "New IEP Drafts" badge
- List of IEP drafts for review
- "View IEP" and "Add Feedback" buttons

### Principal Dashboard
- "IEP Drafts for Review" section
- "Pending Approvals" section
- View and approve/reject IEPs

### Parent Dashboard
- "IEP Information" section
- View IEP draft (read-only)
- Meeting schedule
- Confirm attendance

## Status Flow

```
draft 
  ↓ (SPED clicks "Send IEP")
pending_upload 
  ↓ (SPED uploads document)
pending_meeting 
  ↓ (SPED schedules meeting)
meeting_scheduled 
  ↓ (Meeting completed)
meeting_completed 
  ↓ (SPED finalizes IEP)
pending_finalization 
  ↓ (SPED completes finalization)
pending_signatures 
  ↓ (SPED uploads signed document)
pending_guidance_review 
  ↓ (Guidance approves)
pending_approval 
  ↓ (Principal approves)
approved 
  ↓ (System activates)
active
```

## Files Status

### ✅ Completed
1. `database_iep_complete_workflow.sql`
2. `app/views/iep/create.php`
3. `app/views/iep/list.php`
4. `app/views/iep/upload_draft.php`
5. `app/views/iep/schedule_meeting.php` (basic version)
6. `app/views/iep/meetings.php` (basic version)
7. `app/models/IepMeeting.php`
8. `IepController::uploadDraft()`
9. `IepController::reviewDraft()`
10. `IepController::viewDraft()`

### ⏳ TODO (Priority Order)
1. Update `Iep.php` model - add new methods
2. Create `IepFeedback.php` model
3. Create `IepParticipant.php` model
4. Create `app/views/iep/review_draft.php`
5. Update `schedule_meeting.php` - add participants
6. Update `IepController::scheduleMeeting()` - handle participants
7. Create `app/views/iep/confirm_attendance.php`
8. Create `IepController::confirmAttendance()`
9. Create `app/views/iep/record_meeting.php`
10. Create `IepController::recordMeeting()`
11. Create `app/views/iep/finalize.php` (IEP P3 format)
12. Create `IepController::finalize()`
13. Create `app/views/iep/upload_signed.php`
14. Create `IepController::uploadSigned()`
15. Create `app/views/iep/guidance_review.php`
16. Create `IepController::guidanceReview()`
17. Update `app/views/iep/approve.php`
18. Create `IepController::approve()`
19. Update dashboards (Guidance, Principal, Parent)

## Notes
- All existing features remain INTACT
- Only adding new workflow steps
- Database changes are additive (no deletions)
- Notifications sent at each step
- Minimum 3 days notice for meetings
- Parent decline = must reschedule
- All required participants must confirm
- Guidance must review before Principal
- Once approved, IEP cannot be edited (only viewed)


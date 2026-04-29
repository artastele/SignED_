# Mga Requirement sa User - SPED Workflow Implementation

## Unsaon Paggamit ani nga Document

Kini nga document mao ang imong i-lista tanan nga gusto nimong mahitabo, logic, ug functions para sa matag step sa SPED workflow.

**Format para sa matag requirement:**
```
### [Step Number]: [Feature Name]

**Unsa akong gusto mahitabo:**
- [I-describe ang behavior]

**Logic/Rules:**
- [Specific nga rules o conditions]

**Functions nga kinahanglan:**
- [Lista sa functions]

**UI/UX:**
- [Unsaon niya pagtan-aw/pag-lihok]

**Example:**
- [Konkretong example sa flow]
```

---

## Step 1: Parent Enrollment (BEEF Form)

### Current Status: ✅ Nagtrabaho na

**Dugang nga Requirements:**
<!-- I-add diri imong requirements -->

---

## Step 2: Document Upload

### Current Status: ✅ Nagtrabaho na

**Dugang nga Requirements:**
<!-- I-add diri imong requirements -->

---

## Step 3: SPED Verification & Approval

### Current Status: ✅ Nagtrabaho na

**Dugang nga Requirements:**

### **Auto-Generate LRN ug Learner Account**

**Unsa akong gusto mahitabo:**
- Pag ma-approve ang enrollment, system mu-auto generate ug 12-digit LRN (Learner Reference Number)
- Ang LRN mu-serve as username para sa learner's account
- Default password: `default123`
- System mu-create learner account automatically
- Parent mu-receive email notification with:
  - Approval message
  - Learner's LRN (12 digits)
  - Default password
  - Instructions on how to login
- Ang LRN ma-store sa learners table
- Ang LRN pwede gamiton sa "Old Student" function sa BEEF form

**Logic/Rules:**
- LRN format: 12 digits (e.g., 123456789012)
- LRN must be unique (check if exists before generating)
- Auto-generate algorithm: Year (4) + Month (2) + Random (6)
  - Example: 202604123456 (April 2026 + random 6 digits)
- Default password: `default123` (learner can change later)
- Email notification sent immediately after approval
- LRN stored in `learners.lrn` column
- LRN used as login username for learner account

**Functions nga kinahanglan:**
- `generateLRN()` - Generates unique 12-digit LRN
- `checkLRNExists(lrn)` - Checks if LRN already exists
- `createLearnerAccount(learnerId, lrn)` - Creates user account with LRN
- `sendApprovalWithCredentials(parentEmail, learnerName, lrn)` - Sends email with LRN and password
- `validateLRNLogin(lrn, password)` - Validates learner login

**UI/UX:**
- Consistent sa system design (Bootstrap 5, maroon/bluish theme)
- Approval confirmation shows generated LRN
- Email template professional and clear
- Parent dashboard shows learner's LRN after approval

**Email Notification Template:**
```
Subject: Enrollment Approved - Learner Account Created

Dear [Parent Name],

Good news! Your child's enrollment has been approved.

Student: [Learner Name]
Grade: [Grade Level]
Status: Enrolled

LEARNER ACCOUNT CREDENTIALS:
Username (LRN): 123456789012
Password: default123

Your child can now login to the SignED SPED System using these credentials.

IMPORTANT: Please change the password after first login for security.

Login at: [SYSTEM URL]/auth/login

Thank you,
SignED SPED System
```

**Database Changes:**
```sql
-- LRN already exists in learners table, just ensure it's populated
-- Add index for faster lookup
ALTER TABLE learners ADD INDEX idx_lrn (lrn);

-- Ensure user account created for learner
-- User table already has role='learner'
```

**Success Criteria:**
- ✅ LRN auto-generated on approval (12 digits, unique)
- ✅ Learner account created with LRN as username
- ✅ Default password set to 'default123'
- ✅ Parent receives email with credentials
- ✅ LRN stored in learner record
- ✅ LRN can be used for "Old Student" BEEF form
- ✅ Learner can login with LRN and password
- ✅ LRN displayed in Student Records

---

## Step 4: Assessment

### Current Status: ⚠️ Kinahanglan pa Human-on

**Unsa akong gusto mahitabo:**

### **Parent Assessment Form (Based on IEP P1.pdf)**

**Workflow:**
- After ma-enroll ang learner, mu-appear ang "Assessment" navigation sa Parent Dashboard
- Ang assessment ma-"unlock" pag na-enroll na (after Step 3 approval)
- Parent mu-fill sa assessment form (education history ug learner background)
- Form based sa IEP P1.pdf (SIGNED_LIVEFORMS folder)
- Learner background info auto-generated from BEEF data
- Parent pwede i-save as draft ug i-continue later
- Parent pwede i-mark as complete
- Pag complete, SPED teacher mu-receive notification
- SPED teacher pwede mu-view ang submitted assessment
- Assessment becomes read-only after submission

**Logic/Rules:**
- Assessment ma-unlock ra pag learner status = 'enrolled' (after approval)
- Learner background section auto-filled from BEEF data:
  - Name, DOB, Grade, Address
  - Parent info
  - Disability type
  - Medical history (from BEEF)
- Parent mu-fill sa education history:
  - Previous schools attended
  - Academic performance
  - Behavioral observations
  - Special needs identified
- Part A (Education History) - REQUIRED
- Part B (Additional Information) - OPTIONAL
- Pag na-complete, dili na pwede i-edit (view ra)
- SPED teacher mu-receive notification sa dashboard
- SPED teacher pwede mu-view pero dili mu-edit

**Functions nga kinahanglan:**
- `unlockAssessment(learnerId)` - Unlocks assessment for parent (triggered on approval)
- `getAssessmentTemplate()` - Gets form structure from IEP P1.pdf format
- `autoFillLearnerBackground(learnerId)` - Pre-fills data from BEEF
- `saveAssessmentDraft(assessmentId, data)` - Parent saves draft
- `submitAssessment(assessmentId)` - Parent submits (marks complete)
- `notifySPEDTeacher(assessmentId)` - Sends notification to SPED
- `getAssessmentForReview(assessmentId)` - SPED views submitted assessment
- `validateAssessmentComplete(data)` - Checks if Part A complete

**UI/UX:**
- **Consistent sa system design** (Bootstrap 5, maroon/bluish theme, sidebar, header)
- Parent Dashboard:
  - "Assessment" navigation item
  - Badge showing "New" or "Incomplete" if not done
  - Locked icon if not yet enrolled
  - Unlocked after approval
- Assessment Form (based on IEP P1.pdf):
  - Section 1: Learner Background (auto-filled, read-only)
    - Personal information
    - Family background
    - Medical history
  - Section 2: Education History (parent fills)
    - Previous schools
    - Grade levels completed
    - Academic strengths/weaknesses
    - Behavioral observations
    - Learning difficulties
  - Section 3: Additional Information (optional)
    - Special talents/interests
    - Home support
    - Parent concerns
- Multi-step form with progress indicator
- "Save Draft" button sa matag section
- "Submit Assessment" button (enabled when Part A complete)
- Confirmation dialog before submit
- SPED Dashboard:
  - "New Assessments" notification badge
  - List of submitted assessments
  - "View Assessment" button

**Example Flow:**
```
PARENT SIDE:
1. Parent logs in after learner approved
2. Sees "Assessment" in navigation (with "New" badge)
3. Clicks "Assessment"
4. Sees message: "Please complete the assessment for [Learner Name]"
5. Clicks "Start Assessment"
6. Section 1: Learner Background (pre-filled)
   - Name: Allysa Canonizado
   - DOB: January 28, 2021
   - Grade: Kinder
   - Address: (from BEEF)
   - Disability: (from BEEF)
   - [All read-only, from BEEF data]
7. Section 2: Education History
   - Previous School: "None (First time enrollee)"
   - Academic Performance: [Parent fills]
   - Behavioral Observations: [Parent fills]
   - Learning Difficulties: [Parent fills]
   - Clicks "Save Draft"
8. Section 3: Additional Information (optional)
   - Special Interests: [Parent fills]
   - Home Support: [Parent fills]
   - Parent Concerns: [Parent fills]
   - Clicks "Save Draft"
9. Reviews all sections
10. Clicks "Submit Assessment"
11. Confirmation: "Sigurado ka ba? Dili na ni ma-edit pag na-submit."
12. Parent confirms
13. Success message: "Assessment submitted! SPED teacher will review."
14. Assessment now read-only

SPED TEACHER SIDE:
15. SPED teacher sees notification: "New assessment from Ronald Martinez"
16. Clicks notification
17. Opens assessment view page
18. Reviews all sections:
    - Learner background
    - Education history
    - Additional information
19. Can print assessment for records
20. Can proceed to IEP creation (Step 5)
```

**Form Structure (Based on IEP P1.pdf):**
```
┌─────────────────────────────────────────┐
│  ASSESSMENT FORM                        │
│  Based on IEP Part 1                    │
├─────────────────────────────────────────┤
│                                         │
│  SECTION 1: LEARNER BACKGROUND          │
│  (Auto-filled from BEEF - Read Only)    │
│                                         │
│  Name: [Auto-filled]                    │
│  LRN: [Auto-filled]                     │
│  Date of Birth: [Auto-filled]           │
│  Age: [Auto-calculated]                 │
│  Grade Level: [Auto-filled]             │
│  Address: [Auto-filled]                 │
│  Parent/Guardian: [Auto-filled]         │
│  Contact: [Auto-filled]                 │
│  Disability Type: [Auto-filled]         │
│  Medical History: [Auto-filled]         │
│                                         │
│  ─────────────────────────────────────  │
│                                         │
│  SECTION 2: EDUCATION HISTORY           │
│  (Parent fills - REQUIRED)              │
│                                         │
│  Previous Schools Attended:             │
│  [Textarea]                             │
│                                         │
│  Grade Levels Completed:                │
│  [Input]                                │
│                                         │
│  Academic Strengths:                    │
│  [Textarea]                             │
│                                         │
│  Academic Weaknesses:                   │
│  [Textarea]                             │
│                                         │
│  Behavioral Observations:               │
│  [Textarea]                             │
│                                         │
│  Learning Difficulties:                 │
│  [Textarea]                             │
│                                         │
│  [Save Draft]                           │
│                                         │
│  ─────────────────────────────────────  │
│                                         │
│  SECTION 3: ADDITIONAL INFORMATION      │
│  (Optional)                             │
│                                         │
│  Special Talents/Interests:             │
│  [Textarea]                             │
│                                         │
│  Home Support Available:                │
│  [Textarea]                             │
│                                         │
│  Parent Concerns:                       │
│  [Textarea]                             │
│                                         │
│  [Save Draft]                           │
│                                         │
│  ─────────────────────────────────────  │
│                                         │
│  [Submit Assessment]                    │
│                                         │
└─────────────────────────────────────────┘
```

**Database Structure:**
```sql
-- Update assessments table
ALTER TABLE assessments ADD COLUMN submitted_by_parent BOOLEAN DEFAULT FALSE;
ALTER TABLE assessments ADD COLUMN parent_submitted_at TIMESTAMP NULL;
ALTER TABLE assessments ADD COLUMN learner_background JSON NULL; -- Auto-filled from BEEF
ALTER TABLE assessments ADD COLUMN education_history JSON NULL; -- Parent fills
ALTER TABLE assessments ADD COLUMN additional_info JSON NULL; -- Optional

-- Assessment status flow
-- 'unlocked' - Parent can start
-- 'draft' - Parent saving progress
-- 'submitted' - Parent completed, SPED can review
-- 'reviewed' - SPED reviewed
ALTER TABLE assessments MODIFY COLUMN status ENUM(
    'locked',
    'unlocked', 
    'draft', 
    'submitted',
    'reviewed'
) DEFAULT 'locked';
```

**Validation Rules:**
- Assessment locked until learner approved
- Learner background auto-filled, cannot edit
- Education history (Part A) required
- Additional info (Part B) optional
- Cannot submit without Part A complete
- Once submitted, parent cannot edit
- SPED can only view, not edit parent's assessment

**Success Criteria:**
- ✅ Assessment unlocked after learner approval
- ✅ "Assessment" appears in parent navigation
- ✅ Learner background auto-filled from BEEF
- ✅ Parent can fill education history
- ✅ Can save draft and continue later
- ✅ Can submit when Part A complete
- ✅ SPED receives notification
- ✅ SPED can view submitted assessment
- ✅ Form design matches IEP P1.pdf structure
- ✅ UI consistent with system design
- ✅ Assessment read-only after submission

---

## Step 5: IEP Creation (Draft)

**Unsa akong gusto mahitabo:**

### **SPED Teacher Creates IEP Draft (Based on IEP P2.pdf)**

**Workflow:**
- After parent submits assessment, SPED teacher mu-create IEP draft
- Form based sa IEP P2.pdf (SIGNED_LIVEFORMS folder)
- Dili tanan required kay draft pa ra
- SPED teacher pwede i-save as draft multiple times
- Naa'y option: "Print" ug "Send IEP"
- Pag mu-click "Send IEP", mu-redirect sa meeting scheduling (Step 6)

**Logic/Rules:**
- SPED teacher ra ang pwede mu-create IEP draft
- Based sa IEP P2.pdf format
- Dili tanan fields required (draft stage)
- Minimum required para ma-send:
  - At least 1 goal
  - At least 1 service
- Pwede i-edit anytime before final approval
- "Print" option - generates PDF for review
- "Send IEP" option - proceeds to meeting scheduling
- Pag na-send, IEP status mu-change to 'pending_meeting'

**Functions nga kinahanglan:**
- `createIEPDraft(learnerId, assessmentId)` - Creates draft from assessment
- `saveIEPDraft(iepId, data)` - Saves draft progress
- `getIEPDraft(iepId)` - Retrieves draft
- `validateMinimumRequirements(iepData)` - Checks if can send (1 goal, 1 service)
- `printIEPDraft(iepId)` - Generates PDF for review
- `sendIEPForMeeting(iepId)` - Marks ready, proceeds to scheduling
- `getIEPTemplate()` - Gets form structure from IEP P2.pdf

**UI/UX:**
- **Consistent sa system design** (Bootstrap 5, maroon/bluish theme)
- IEP Draft Form (based on IEP P2.pdf):
  - Student information (pre-filled)
  - Present levels of performance
  - Annual goals (can add multiple)
  - Special education services (can add multiple)
  - Accommodations
  - Assessment accommodations
- "Save Draft" button (always visible)
- "Print" button (generates PDF preview)
- "Send IEP" button (enabled when minimum requirements met)
- Confirmation before sending

**Example Flow:**
```
1. SPED teacher receives notification: "Assessment submitted by parent"
2. Clicks "Create IEP Draft"
3. Opens IEP draft form (based on IEP P2.pdf)
4. Section 1: Student Info (pre-filled from assessment)
5. Section 2: Present Levels
   - Teacher enters current performance
   - Clicks "Save Draft"
6. Section 3: Annual Goals
   - Clicks "Add Goal"
   - Enters goal description
   - Adds measurable objectives
   - Clicks "Save Goal"
   - Clicks "Save Draft"
7. Section 4: Services
   - Clicks "Add Service"
   - Selects service type
   - Sets frequency/duration
   - Clicks "Save Service"
   - Clicks "Save Draft"
8. Section 5: Accommodations (optional for draft)
   - Can add later
9. Teacher reviews draft
10. Clicks "Print" to review PDF
11. Reviews printed version
12. Clicks "Send IEP"
13. Confirmation: "Send IEP and schedule meeting?"
14. Teacher confirms
15. IEP status → 'pending_meeting'
16. Redirects to Meeting Scheduling page
```

**Success Criteria:**
- ✅ Can create IEP draft from assessment
- ✅ Form based on IEP P2.pdf structure
- ✅ Can save draft multiple times
- ✅ Can print draft for review
- ✅ Can send IEP when minimum requirements met
- ✅ Redirects to meeting scheduling
- ✅ UI consistent with system design

---

## Step 6: IEP Meeting Scheduling & Document Distribution

### Current Status: ⚠️ Kinahanglan pa Human-on

**Unsa akong gusto mahitabo:**

### **Part A: Upload IEP Draft Document & Send to Reviewers**

**Workflow:**
- After SPED teacher mu-click "Send IEP" (from Step 5)
- Mu-redirect sa document upload page
- SPED teacher mu-upload IEP draft document (PDF)
- System mu-send IEP draft to:
  - Guidance Counselor (for feedback)
  - Principal (for review)
  - Parent (for information)
- Each recipient receives email notification with link to view IEP draft
- Guidance can provide feedback/comments
- Principal can review and provide initial comments
- Parent can view but cannot edit

**Logic/Rules:**
- IEP draft document required before scheduling meeting
- Accepted formats: PDF only
- Max file size: 10MB
- Document stored in document_store table
- Linked to IEP record
- Notifications sent immediately after upload
- Guidance feedback optional but recommended
- Principal review and approve or not 
- Parent can only view, not edit

**Functions nga kinahanglan:**
- `uploadIEPDraft(iepId, file)` - Uploads IEP draft document
- `linkIEPDocument(iepId, documentId)` - Links document to IEP
- `sendIEPToReviewers(iepId)` - Sends to Guidance, Principal, Parent
- `notifyGuidance(iepId)` - Sends notification to Guidance
- `notifyPrincipal(iepId)` - Sends notification to Principal
- `notifyParent(iepId)` - Sends notification to Parent
- `addGuidanceFeedback(iepId, feedback)` - Guidance adds comments
- `viewIEPDraft(iepId, userId)` - View IEP draft with role-based access

**UI/UX:**
- Upload IEP Draft Page:
  - File upload dropzone
  - "Upload IEP Draft" button
  - Preview uploaded document
  - "Send to Reviewers" button
  - List of recipients with checkboxes:
    - ☑ Guidance Counselor (required)
    - ☑ Principal (required)
    - ☑ Parent (required)
- Guidance Dashboard:
  - "New IEP Drafts" notification badge
  - List of IEP drafts for review
  - "View IEP" button
  - "Add Feedback" button
  - Feedback form (textarea)
- Principal Dashboard:
  - "IEP Drafts for Review" section
  - View IEP draft
  - Add comments (optional at this stage)
- Parent Dashboard:
  - "IEP Draft Available" notification
  - "View IEP Draft" button
  - Read-only view

**Email Notification Templates:**

**For Guidance:**
```
Subject: IEP Draft for Review - [Student Name]

Dear [Guidance Counselor Name],

A new IEP draft is ready for your review:

Student: Allysa Canonizado
Grade: Kinder
SPED Teacher: Maria Santos

Please review the IEP draft and provide feedback.

[View IEP Draft] [Add Feedback]

Thank you,
SignED SPED System
```

**For Principal:**
```
Subject: IEP Draft for Review - [Student Name]

Dear [Principal Name],

A new IEP draft has been created:

Student: Allysa Canonizado
Grade: Kinder
SPED Teacher: Maria Santos

Please review the IEP draft.

[View IEP Draft]

A meeting will be scheduled soon.

Thank you,
SignED SPED System
```

**For Parent:**
```
Subject: IEP Draft Available - [Student Name]

Dear [Parent Name],

Your child's IEP draft is now available for review:

Student: Allysa Canonizado
Grade: Kinder

You can view the IEP draft in your parent dashboard.

[View IEP Draft]

A meeting will be scheduled to discuss the IEP.

Thank you,
SignED SPED System
```

### **Part B: Schedule IEP Meeting**

**Workflow:**
- After uploading IEP draft, SPED teacher mu-schedule meeting
- Mu-select date, time, location
- Mu-invite participants:
  - Parent/Guardian (required)
  - SPED Teacher (required)
  - Guidance Counselor (required)
  - General Ed Teacher (optional)
  - Principal/Administrator (required)
  - Other specialists (optional)
- System mu-send email notifications sa tanan
- Participants pwede mu-confirm attendance
- Calendar view shows all scheduled meetings
- Can edit/reschedule meeting
- Can cancel meeting with reason
- On meeting day, teacher mu-record meeting notes
- After meeting, mu-mark as completed
- System mu-update learner status to 'iep_meeting_complete'
- Mu-proceed sa IEP finalization (Step 7)

**Logic/Rules:**
- Kinahanglan naa'y uploaded IEP draft before scheduling
- Meeting date kinahanglan future date (not past)
- Minimum 3 days notice before meeting
- Required participants:
  - Parent/Guardian
  - SPED Teacher
  - Guidance Counselor
  - Principal/Administrator
- Optional participants:
  - General Ed Teacher
  - Other specialists
- Participants pwede mu-confirm or decline
- Kung parent mu-decline, kinahanglan mu-reschedule
- Can edit meeting details before meeting date
- Can cancel meeting with reason (notifies all participants)
- Meeting notes required before marking as complete
- Pag na-complete ang meeting, IEP status mu-change to 'pending_finalization'
- Learner status mu-change to 'iep_meeting_complete'
- Calendar view with CRUD functions:
  - **Create** - Schedule new meeting
  - **Read** - View meeting details
  - **Update** - Edit meeting (date, time, participants)
  - **Delete** - Cancel meeting

**Functions nga kinahanglan:**
- `uploadIEPDraft(iepId, file)` - Uploads IEP draft document
- `sendIEPToReviewers(iepId)` - Sends to Guidance, Principal, Parent
- `addGuidanceFeedback(iepId, feedback)` - Guidance adds comments
- `scheduleMeeting(iepId, meetingData)` - Creates meeting schedule
- `updateMeeting(meetingId, meetingData)` - Updates meeting details (CRUD - Update)
- `cancelMeeting(meetingId, reason)` - Cancels meeting (CRUD - Delete)
- `inviteParticipant(meetingId, userId, role)` - Adds participant
- `removeParticipant(meetingId, userId)` - Removes participant
- `sendMeetingInvitations(meetingId)` - Sends email notifications
- `confirmAttendance(meetingId, userId, status)` - Participant confirms/declines
- `rescheduleMeeting(meetingId, newDate, reason)` - Reschedules meeting
- `recordMeetingNotes(meetingId, notes)` - Saves meeting notes
- `completeMeeting(meetingId)` - Marks meeting as complete
- `getMeetingsByStatus(status)` - Gets meetings by status (CRUD - Read)
- `getUpcomingMeetings()` - Gets upcoming meetings (CRUD - Read)
- `getMeetingDetails(meetingId)` - Gets full meeting info (CRUD - Read)
- `getCalendarView(month, year)` - Gets calendar view of meetings

**UI/UX:**
- **Consistent sa system design** (Bootstrap 5, maroon/bluish theme, sidebar, header)

**Upload IEP Draft Page:**
- File upload dropzone with drag & drop
- Preview uploaded PDF
- "Send to Reviewers" button
- Recipient list with status indicators

**Calendar View (Main Feature):**
- Monthly calendar grid
- Color-coded meetings:
  - 🔴 Red - Pending confirmation
  - 🟡 Yellow - Confirmed, upcoming
  - 🟢 Green - Completed
  - ⚫ Gray - Cancelled
- Click date to create new meeting
- Click meeting to view details
- Filter by status (all, upcoming, completed, cancelled)
- Navigation: Previous/Next month

**Meeting Scheduling Form:**
- Date picker (minimum 3 days from today)
- Time picker (8 AM - 5 PM)
- Location/Room input
- Duration (default 1 hour)
- Required Participants (auto-selected):
  - ☑ Parent/Guardian
  - ☑ SPED Teacher
  - ☑ Guidance Counselor
  - ☑ Principal
- Optional Participants:
  - ☐ General Ed Teacher
  - ☐ Other specialists
- Agenda/Notes textarea
- "Schedule Meeting" button
- "Cancel" button

**Meeting List View:**
- Table with columns:
  - Student Name
  - Date & Time
  - Location
  - Status
  - Attendance (confirmed/total)
  - Actions (View, Edit, Cancel)
- Filters: Upcoming, Today, This Week, Completed
- Search by student name

**Meeting Details Page:**
- Meeting info card:
  - Student name & LRN
  - Date, time, location
  - Duration
  - Status badge
- Participant list with attendance status:
  - ✅ Confirmed
  - ⏳ Pending
  - ❌ Declined
- IEP draft document link
- Guidance feedback section (if available)
- Agenda/Notes
- Action buttons:
  - "Edit Meeting" (before meeting date)
  - "Cancel Meeting" (before meeting date)
  - "Record Meeting Notes" (on/after meeting date)
  - "Complete Meeting" (after notes recorded)
  - "Print Meeting Details"

**Meeting Notes Form:**
- Attendees checklist (who actually attended)
- Discussion summary (textarea)
- Decisions made (textarea)
- Parent feedback (textarea)
- Next steps (textarea)
- "Save Notes" button

**Email Notification Template (Meeting Invitation):**
```
Subject: IEP Meeting Invitation - [Student Name]

Dear [Participant Name],

You are invited to attend an IEP meeting for:

Student: Allysa Canonizado
LRN: 202604123456
Grade: Kinder

Meeting Details:
Date: May 5, 2026
Time: 2:00 PM
Location: Conference Room A
Duration: 1 hour

Agenda:
Discuss Allysa's IEP goals, services, and accommodations

IEP Draft: [View Document]

Please confirm your attendance:
[Confirm Attendance] [Decline]

If you need to reschedule, please contact Maria Santos (SPED Teacher).

Thank you,
SignED SPED System
```

**Example Flow:**

**Part A: Upload & Send IEP Draft**
```
1. SPED teacher completes IEP draft (Step 5)
2. Clicks "Send IEP"
3. Redirects to "Upload IEP Draft" page
4. Uploads PDF document (IEP_Allysa_Draft.pdf)
5. System validates file (PDF, <10MB)
6. Preview shows uploaded document
7. Recipient list shows:
   - ☑ Guidance Counselor: Dr. Reyes
   - ☑ Principal: Dr. Garcia
   - ☑ Parent: Ronald Martinez
8. Clicks "Send to Reviewers"
9. System:
   - Stores document in document_store
   - Links to IEP record
   - Sends email notifications to all 3
   - Updates IEP status to 'sent_for_review'
10. Success message: "IEP draft sent! You can now schedule a meeting."
11. Redirects to Meeting Scheduling page

GUIDANCE SIDE:
12. Dr. Reyes receives email notification
13. Clicks "View IEP Draft"
14. Reviews IEP document
15. Clicks "Add Feedback"
16. Enters feedback:
    "Goals are appropriate. Suggest adding speech therapy service."
17. Clicks "Submit Feedback"
18. SPED teacher receives notification of feedback

PRINCIPAL SIDE:
19. Dr. Garcia receives email notification
20. Clicks "View IEP Draft"
21. Reviews IEP document
22. Can add comments (optional at this stage)

PARENT SIDE:
23. Ronald Martinez receives email notification
24. Logs in to parent dashboard
25. Sees "IEP Draft Available" notification
26. Clicks "View IEP Draft"
27. Reviews document (read-only)
```

**Part B: Schedule Meeting**
```
1. SPED teacher on Meeting Scheduling page
2. Selects date: May 5, 2026 (3 days from now)
3. Selects time: 2:00 PM
4. Enters location: Conference Room A
5. Duration: 1 hour (default)
6. Required participants (auto-selected):
   - ☑ Parent: Ronald Martinez
   - ☑ SPED Teacher: Maria Santos
   - ☑ Guidance: Dr. Reyes
   - ☑ Principal: Dr. Garcia
7. Optional participants:
   - ☑ Gen Ed Teacher: Juan Dela Cruz
8. Enters agenda:
   "Discuss Allysa's IEP goals, services, and accommodations.
    Review Guidance feedback on speech therapy."
9. Clicks "Schedule Meeting"
10. System:
    - Creates meeting record
    - Sends email invitations to all 5 participants
    - Updates IEP status to 'meeting_scheduled'
11. Success message: "Meeting scheduled! Invitations sent."
12. Redirects to Calendar View

PARTICIPANT CONFIRMATION:
13. All participants receive email
14. Ronald (Parent) clicks "Confirm Attendance"
15. System updates attendance: Ronald ✅ Confirmed
16. Maria (SPED) auto-confirmed (organizer)
17. Dr. Reyes (Guidance) clicks "Confirm Attendance"
18. Dr. Garcia (Principal) clicks "Confirm Attendance"
19. Juan (Gen Ed) clicks "Decline" with reason: "Schedule conflict"
20. System sends notification to Maria about Juan's decline
21. Maria can reschedule or proceed without Juan

CALENDAR VIEW:
22. Maria opens Calendar View
23. Sees May 2026 calendar
24. May 5 shows yellow dot (confirmed, upcoming)
25. Clicks on May 5
26. Sees meeting details popup
27. Clicks "View Full Details"
28. Opens Meeting Details page

MEETING DAY (May 5, 2026):
29. Meeting happens (physical meeting)
30. All confirmed participants attend
31. Maria opens Meeting Details page
32. Clicks "Record Meeting Notes"
33. Fills meeting notes form:
    - Attendees: ✅ Parent, ✅ SPED, ✅ Guidance, ✅ Principal
    - Discussion: "Reviewed IEP goals. Parent agreed with all goals.
                   Added speech therapy service per Guidance recommendation.
                   Principal approved the plan."
    - Decisions: "Speech therapy added: 2x/week, 30 min sessions"
    - Parent feedback: "Very satisfied with the plan. Ready to proceed."
    - Next steps: "Finalize IEP with speech therapy. Print and sign."
34. Clicks "Save Notes"
35. Clicks "Complete Meeting"
36. Confirmation: "Mark this meeting as complete?"
37. Maria confirms
38. System updates:
    - Meeting status → 'completed'
    - IEP status → 'pending_finalization'
    - Learner status → 'iep_meeting_complete'
39. Success message: "Meeting completed! Please finalize the IEP."
40. Redirects to IEP Finalization page (Step 7)
```

**Database Structure:**
```sql
-- IEP document tracking
ALTER TABLE ieps ADD COLUMN draft_document_id INT NULL;
ALTER TABLE ieps ADD COLUMN sent_for_review_at TIMESTAMP NULL;
ALTER TABLE ieps ADD COLUMN guidance_feedback TEXT NULL;
ALTER TABLE ieps ADD COLUMN guidance_feedback_at TIMESTAMP NULL;
ALTER TABLE ieps ADD COLUMN guidance_feedback_by INT NULL;

-- Foreign keys
ALTER TABLE ieps ADD FOREIGN KEY (draft_document_id) 
    REFERENCES document_store(id);
ALTER TABLE ieps ADD FOREIGN KEY (guidance_feedback_by) 
    REFERENCES users(id);

-- IEP Meetings table
CREATE TABLE IF NOT EXISTS iep_meetings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NOT NULL,
    scheduled_by INT NOT NULL,
    
    -- Meeting details
    meeting_date DATE NOT NULL,
    meeting_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    duration INT DEFAULT 60, -- minutes
    agenda TEXT NULL,
    
    -- Participants (stored as JSON)
    -- Format: [{"user_id": 1, "role": "parent", "name": "Ronald Martinez", "required": true, "status": "confirmed"}]
    participants JSON NULL,
    
    -- Attendance tracking
    attendance_confirmed JSON NULL,
    actual_attendees JSON NULL, -- Who actually attended
    
    -- Meeting notes
    meeting_notes TEXT NULL,
    discussion_summary TEXT NULL,
    decisions_made TEXT NULL,
    parent_feedback TEXT NULL,
    next_steps TEXT NULL,
    
    -- Status
    status ENUM(
        'scheduled',      -- Meeting scheduled, pending confirmations
        'confirmed',      -- All required participants confirmed
        'in_progress',    -- Meeting happening now
        'completed',      -- Meeting done, notes recorded
        'cancelled',      -- Meeting cancelled
        'rescheduled'     -- Meeting rescheduled
    ) DEFAULT 'scheduled',
    
    -- Cancellation
    cancelled_reason TEXT NULL,
    cancelled_by INT NULL,
    cancelled_at TIMESTAMP NULL,
    
    -- Timestamps
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (iep_id) REFERENCES ieps(id),
    FOREIGN KEY (scheduled_by) REFERENCES users(id),
    FOREIGN KEY (cancelled_by) REFERENCES users(id)
);

-- Meeting reminders
CREATE TABLE IF NOT EXISTS meeting_reminders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    meeting_id INT NOT NULL,
    user_id INT NOT NULL,
    reminder_type ENUM('3_days', '1_day', '1_hour') NOT NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (meeting_id) REFERENCES iep_meetings(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Update IEP status enum to include new statuses
ALTER TABLE ieps MODIFY COLUMN status ENUM(
    'draft',                  -- SPED creating draft
    'sent_for_review',        -- Sent to Guidance/Principal/Parent
    'meeting_scheduled',      -- Meeting scheduled
    'meeting_completed',      -- Meeting done
    'pending_finalization',   -- Ready for final IEP (Step 7)
    'pending_signatures',     -- Waiting for signatures
    'signed',                 -- All signed
    'approved',               -- Principal approved
    'active',                 -- Active IEP
    'rejected'                -- Rejected
) DEFAULT 'draft';
```

**Email Notification Template:**
```
Subject: IEP Meeting Invitation - [Student Name]

Dear [Participant Name],

You are invited to attend an IEP meeting for:

Student: Allysa Canonizado
Grade: Kinder

Meeting Details:
Date: May 5, 2026
Time: 2:00 PM
Location: Conference Room A
Duration: 1 hour

Agenda:
Discuss Allysa's IEP goals and services

Please confirm your attendance:
[Confirm Attendance] [Decline]

If you need to reschedule, please contact [SPED Teacher Name].

Thank you,
SignED SPED System
```

**Validation Rules:**
- IEP draft document must be uploaded before scheduling
- Meeting date must be at least 3 days in future
- Meeting time must be during school hours (8 AM - 5 PM)
- Required participants must be invited:
  - Parent/Guardian
  - SPED Teacher
  - Guidance Counselor
  - Principal
- Cannot complete meeting without recording notes
- Cannot complete meeting if parent declined (must reschedule)
- Must reschedule if parent cannot attend
- Can only edit meeting before meeting date
- Can only cancel meeting before meeting date

**Success Criteria:**
- ✅ Can upload IEP draft document (PDF)
- ✅ IEP draft sent to Guidance, Principal, Parent
- ✅ Email notifications sent automatically
- ✅ Guidance can provide feedback
- ✅ Principal can review draft
- ✅ Parent can view draft (read-only)
- ✅ Can schedule meeting with date/time/location
- ✅ Can invite required and optional participants
- ✅ Calendar view shows all meetings (CRUD - Read)
- ✅ Can create new meeting from calendar (CRUD - Create)
- ✅ Can edit meeting details before meeting date (CRUD - Update)
- ✅ Can cancel meeting with reason (CRUD - Delete)
- ✅ Participants can confirm/decline attendance
- ✅ Can reschedule if needed
- ✅ Can record meeting notes
- ✅ Can mark meeting as complete
- ✅ IEP and learner status update automatically
- ✅ Redirects to IEP finalization (Step 7)
- ✅ UI consistent with system design

---

## Step 7: IEP Finalization & Approval

### Current Status: ⚠️ Kinahanglan pa Human-on

**Unsa akong gusto mahitabo:**

### **Part A: SPED Finalizes IEP (Based on IEP P3.pdf)**

**Workflow:**
- After meeting completed, SPED teacher mu-finalize IEP
- Form based sa IEP P3.pdf (SIGNED_LIVEFORMS folder)
- **ALL fields required** for finalization (unlike draft)
- Includes meeting decisions and feedback
- Incorporates Guidance feedback
- Updates goals/services based on meeting discussion
- Must complete all sections before finalizing
- Once finalized, IEP ready for signatures

**Logic/Rules:**
- Can only finalize after meeting completed
- Based on IEP P3.pdf format (complete version)
- All sections required:
  - Student information
  - Present levels of performance
  - Annual goals (with measurable objectives)
  - Special education services (with frequency/duration)
  - Accommodations (classroom and assessment)
  - Transition services (if applicable)
  - Progress monitoring plan
- Must incorporate meeting decisions
- Must address Guidance feedback
- Validation checks all required fields
- Cannot proceed to signatures without complete IEP
- Once finalized, status changes to 'pending_signatures'

**Functions nga kinahanglan:**
- `finalizeIEP(iepId, finalData)` - Finalizes IEP with complete data
- `validateIEPComplete(iepData)` - Checks all required fields
- `incorporateMeetingDecisions(iepId, meetingId)` - Adds meeting outcomes
- `incorporateGuidanceFeedback(iepId)` - Includes Guidance comments
- `generateFinalIEP(iepId)` - Creates final IEP document
- `markReadyForSignatures(iepId)` - Changes status to pending_signatures

**UI/UX:**
- **Consistent sa system design** (Bootstrap 5, maroon/bluish theme)
- IEP Finalization Form (based on IEP P3.pdf):
  - All sections from draft, now ALL required
  - Meeting decisions section (auto-filled from meeting notes)
  - Guidance feedback section (auto-filled)
  - Progress indicator showing completion (e.g., 8/8 sections)
  - "Save Progress" button
  - "Finalize IEP" button (enabled when all sections complete)
- Validation messages for incomplete sections
- Confirmation before finalizing

### **Part B: Print & Sign Approach**

**Workflow:**
1. SPED teacher mu-finalize IEP (Part A above)
2. System mu-generate printable IEP document (based on IEP P3.pdf)
3. Naa'y "Print IEP" button
4. Document mu-print with signature lines para sa:
   - Parent/Guardian
   - SPED Teacher
   - Guidance Counselor
   - General Ed Teacher (if applicable)
   - Principal/Administrator
5. SPED teacher mu-print document
6. During/after meeting, tanan mu-sign sa printed document
7. SPED teacher/Admin mu-scan ang signed document
8. Mu-upload balik sa system as "Signed IEP"
9. Guidance Counselor mu-review ang uploaded signed copy
10. Guidance provides final feedback/approval
11. Principal mu-review ang uploaded signed copy
12. Principal mu-approve sa system
13. Learner status mu-update to 'active'
14. IEP now active and implemented

**Logic/Rules:**
- Kinahanglan finalized IEP before printing
- Print format based on IEP P3.pdf
- Signature lines for all required signatories
- Kinahanglan naa'y uploaded signed copy before approval
- Accepted formats: PDF, JPG, PNG
- Max file size: 10MB
- Document stored in document_store table
- Linked to IEP record
- **Guidance must review first** before Principal
- Guidance can:
  - Approve (proceed to Principal)
  - Request revisions (back to SPED)
- Principal can:
  - Approve (IEP becomes active)
  - Reject (back to SPED with reason)
- Once approved, IEP status → 'approved'
- Learner status → 'active'
- Email notifications sent to all parties
- Active IEP cannot be edited (only viewed)

**Functions nga kinahanglan:**
- `finalizeIEP(iepId, finalData)` - Completes IEP with all required fields
- `generatePrintableIEP(iepId)` - Creates print-friendly PDF with signature lines
- `uploadSignedIEP(iepId, file)` - Uploads scanned signed document
- `linkSignedDocument(iepId, documentId)` - Links signed copy to IEP
- `guidanceReviewIEP(iepId, action, feedback)` - Guidance approves or requests revisions
- `principalApproveIEP(iepId)` - Principal final approval
- `principalRejectIEP(iepId, reason)` - Principal rejection with feedback
- `requestIEPRevisions(iepId, reason)` - Send back to SPED for changes
- `updateLearnerStatus(learnerId, status)` - Updates to 'active'
- `sendApprovalNotifications(iepId)` - Notifies all parties
- `activateIEP(iepId)` - Makes IEP active and read-only

**UI/UX:**
- **Consistent sa system design** (Bootstrap 5, maroon/bluish theme)

**IEP Finalization Page:**
- Complete IEP form (all sections required)
- Progress indicator: "8/8 sections complete"
- "Finalize IEP" button (enabled when complete)
- Confirmation dialog before finalizing

**Print IEP Page:**
- "Print IEP" button (large, prominent)
- Preview of printable document
- Print layout includes:
  - School logo/letterhead
  - Complete IEP content (all sections)
  - Signature section with lines:
    ```
    SIGNATURES
    
    Parent/Guardian: _________________ Date: _______
    
    SPED Teacher: ___________________ Date: _______
    
    Guidance Counselor: ______________ Date: _______
    
    General Ed Teacher: ______________ Date: _______
    (if applicable)
    
    Principal: ______________________ Date: _______
    ```
  - Print-friendly CSS (no sidebar/buttons)

**Upload Signed IEP Page:**
- File upload dropzone
- "Upload Signed IEP" button
- Preview uploaded document
- Status: "Pending Guidance Review"

**Guidance Review Page:**
- View signed IEP document
- Review checklist:
  - ☐ All signatures present
  - ☐ Goals appropriate
  - ☐ Services adequate
  - ☐ Accommodations suitable
- Feedback textarea
- Action buttons:
  - "Approve & Send to Principal" (green)
  - "Request Revisions" (yellow)
- Confirmation dialogs

**Principal Approval Page:**
- View signed IEP document
- View Guidance approval/feedback
- Review summary:
  - Student info
  - Goals count
  - Services count
  - Meeting date
  - Guidance approval date
- Action buttons:
  - "Approve IEP" (green, large)
  - "Reject IEP" (red)
- Rejection requires reason (textarea)
- Confirmation dialogs

**Status Flow Indicator:**
```
Draft → Sent for Review → Meeting Scheduled → 
Meeting Completed → Finalized → Pending Signatures → 
Signed → Guidance Review → Principal Review → Approved → Active
```

**Example Flow:**

**Part A: Finalize IEP**
```
1. After meeting completed, SPED teacher receives notification
2. Clicks "Finalize IEP"
3. Opens IEP Finalization Form (based on IEP P3.pdf)
4. Reviews all sections:
   - Section 1: Student Info ✅ (complete)
   - Section 2: Present Levels ✅ (complete)
   - Section 3: Annual Goals ⚠️ (needs update from meeting)
   - Section 4: Services ⚠️ (needs speech therapy added)
   - Section 5: Accommodations ❌ (incomplete)
   - Section 6: Assessment Accommodations ❌ (incomplete)
   - Section 7: Transition Services ❌ (incomplete)
   - Section 8: Progress Monitoring ❌ (incomplete)
5. Updates Section 3 (Goals):
   - Incorporates meeting decisions
   - Adds measurable objectives
6. Updates Section 4 (Services):
   - Adds speech therapy: 2x/week, 30 min
   - Per Guidance recommendation
7. Completes Section 5 (Accommodations):
   - Extended time on tests
   - Preferential seating
   - Visual aids
8. Completes Section 6 (Assessment Accommodations):
   - Read-aloud option
   - Breaks as needed
9. Completes Section 7 (Transition Services):
   - N/A for Kinder (or age-appropriate)
10. Completes Section 8 (Progress Monitoring):
    - Weekly progress reports
    - Monthly parent meetings
    - Quarterly assessments
11. Progress indicator: 8/8 sections complete ✅
12. Clicks "Finalize IEP"
13. Confirmation: "Finalize IEP? This will lock the document for signatures."
14. Teacher confirms
15. System:
    - Validates all required fields
    - Updates IEP status to 'pending_signatures'
    - Generates final IEP document
16. Success message: "IEP finalized! Ready for printing and signatures."
17. Redirects to Print IEP page
```

**Part B: Print & Sign**
```
18. SPED teacher on Print IEP page
19. Reviews printable document preview
20. Clicks "Print IEP"
21. Document prints with:
    - All IEP content
    - Signature lines for all parties
22. Teacher brings printed IEP to meeting (or after meeting)
23. All parties sign:
    - Parent: Ronald Martinez ✍️
    - SPED Teacher: Maria Santos ✍️
    - Guidance: Dr. Reyes ✍️
    - Principal: Dr. Garcia ✍️
24. Teacher scans signed document
25. Opens "Upload Signed IEP" page
26. Uploads scanned PDF
27. System validates file (PDF, <10MB)
28. Preview shows uploaded document
29. Clicks "Submit for Review"
30. System:
    - Stores in document_store
    - Links to IEP record
    - Updates status to 'signed'
    - Sends notification to Guidance
31. Success message: "Signed IEP uploaded! Sent to Guidance for review."
```

**Part C: Guidance Review**
```
32. Dr. Reyes (Guidance) receives notification
33. Clicks "Review Signed IEP"
34. Opens Guidance Review page
35. Views signed IEP document
36. Reviews checklist:
    - ✅ All signatures present
    - ✅ Goals appropriate
    - ✅ Services adequate (speech therapy added)
    - ✅ Accommodations suitable
37. Enters feedback:
    "All requirements met. Speech therapy appropriately included.
     Goals are measurable and achievable. Recommend approval."
38. Clicks "Approve & Send to Principal"
39. Confirmation: "Approve IEP and send to Principal?"
40. Dr. Reyes confirms
41. System:
    - Updates status to 'guidance_approved'
    - Sends notification to Principal
42. Success message: "IEP approved! Sent to Principal for final approval."
```

**Part D: Principal Approval**
```
43. Dr. Garcia (Principal) receives notification
44. Clicks "Review IEP for Approval"
45. Opens Principal Approval page
46. Views:
    - Signed IEP document
    - Guidance approval & feedback
    - Review summary
47. Reviews all sections
48. Sees Guidance recommendation: "Recommend approval"
49. Clicks "Approve IEP"
50. Confirmation: "Approve this IEP? Learner will become active."
51. Dr. Garcia confirms
52. System:
    - Updates IEP status to 'approved'
    - Updates learner status to 'active'
    - Sends notifications to:
      - Parent (email)
      - SPED Teacher (email + dashboard)
      - Guidance (email)
    - Creates audit log entry
53. Success message: "IEP approved! Learner is now active."
54. IEP now read-only, active, and ready for implementation
```

**Email Notifications:**

**To Parent:**
```
Subject: IEP Approved - [Student Name]

Dear Ronald Martinez,

Great news! Your child's IEP has been approved.

Student: Allysa Canonizado
LRN: 202604123456
Grade: Kinder
Status: Active

Your child's Individualized Education Program is now active
and will be implemented starting [Start Date].

You can view the approved IEP in your parent dashboard.

[View IEP]

If you have any questions, please contact Maria Santos (SPED Teacher).

Thank you,
SignED SPED System
```

**To SPED Teacher:**
```
Subject: IEP Approved - [Student Name]

Dear Maria Santos,

The IEP for Allysa Canonizado has been approved by Principal Dr. Garcia.

Student: Allysa Canonizado
LRN: 202604123456
Status: Active

You can now begin implementing the IEP.

[View IEP] [Print IEP]

Thank you,
SignED SPED System
```

**Database Changes:**
```sql
-- IEP finalization and approval tracking
ALTER TABLE ieps ADD COLUMN finalized_at TIMESTAMP NULL;
ALTER TABLE ieps ADD COLUMN finalized_by INT NULL;
ALTER TABLE ieps ADD COLUMN signed_document_id INT NULL;
ALTER TABLE ieps ADD COLUMN signed_at TIMESTAMP NULL;
ALTER TABLE ieps ADD COLUMN uploaded_by INT NULL;

-- Guidance review
ALTER TABLE ieps ADD COLUMN guidance_reviewed_at TIMESTAMP NULL;
ALTER TABLE ieps ADD COLUMN guidance_reviewed_by INT NULL;
ALTER TABLE ieps ADD COLUMN guidance_review_status ENUM('pending', 'approved', 'revisions_requested') DEFAULT 'pending';
ALTER TABLE ieps ADD COLUMN guidance_review_feedback TEXT NULL;

-- Principal approval
ALTER TABLE ieps ADD COLUMN principal_approved_at TIMESTAMP NULL;
ALTER TABLE ieps ADD COLUMN principal_approved_by INT NULL;
ALTER TABLE ieps ADD COLUMN principal_rejection_reason TEXT NULL;

-- IEP content (JSON format for flexibility)
ALTER TABLE ieps ADD COLUMN iep_content JSON NULL;
-- Structure:
-- {
--   "student_info": {...},
--   "present_levels": {...},
--   "annual_goals": [{goal1}, {goal2}, ...],
--   "services": [{service1}, {service2}, ...],
--   "accommodations": {...},
--   "assessment_accommodations": {...},
--   "transition_services": {...},
--   "progress_monitoring": {...}
-- }

-- Foreign keys
ALTER TABLE ieps ADD FOREIGN KEY (finalized_by) REFERENCES users(id);
ALTER TABLE ieps ADD FOREIGN KEY (signed_document_id) REFERENCES document_store(id);
ALTER TABLE ieps ADD FOREIGN KEY (uploaded_by) REFERENCES users(id);
ALTER TABLE ieps ADD FOREIGN KEY (guidance_reviewed_by) REFERENCES users(id);
ALTER TABLE ieps ADD FOREIGN KEY (principal_approved_by) REFERENCES users(id);

-- Update IEP status enum (complete version)
ALTER TABLE ieps MODIFY COLUMN status ENUM(
    'draft',                    -- SPED creating draft (Step 5)
    'sent_for_review',          -- Sent to Guidance/Principal/Parent (Step 6)
    'meeting_scheduled',        -- Meeting scheduled (Step 6)
    'meeting_completed',        -- Meeting done (Step 6)
    'pending_finalization',     -- Ready for final IEP (Step 7)
    'finalized',                -- SPED completed all sections (Step 7)
    'pending_signatures',       -- Waiting for physical signatures (Step 7)
    'signed',                   -- Signed copy uploaded (Step 7)
    'guidance_review',          -- Guidance reviewing (Step 7)
    'guidance_approved',        -- Guidance approved (Step 7)
    'principal_review',         -- Principal reviewing (Step 7)
    'approved',                 -- Principal approved (Step 7)
    'active',                   -- Active IEP (Step 7)
    'revisions_requested',      -- Sent back for changes
    'rejected'                  -- Rejected by Principal
) DEFAULT 'draft';

-- Audit log for IEP status changes
CREATE TABLE IF NOT EXISTS iep_audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    old_status VARCHAR(50) NULL,
    new_status VARCHAR(50) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (iep_id) REFERENCES ieps(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**Validation Rules:**
- Cannot finalize without completing all 8 sections
- Cannot print without finalizing first
- Cannot upload signed copy without printing first
- Signed copy must be valid file format (PDF, JPG, PNG)
- Max file size: 10MB
- Guidance must review before Principal
- Guidance can approve or request revisions
- If revisions requested, goes back to SPED (status: revisions_requested)
- Principal can only review after Guidance approval
- Principal can approve or reject
- If rejected, goes back to SPED with reason
- Only Principal/Admin can give final approval
- Once approved, IEP becomes read-only
- Active IEP cannot be edited (only viewed or printed)
- All status changes logged in audit trail

**Success Criteria:**
- ✅ Can finalize IEP with all required fields (based on IEP P3.pdf)
- ✅ All 8 sections must be complete
- ✅ Validation prevents incomplete finalization
- ✅ Can print IEP with signature lines
- ✅ Print format matches IEP P3.pdf structure
- ✅ Can upload scanned signed copy
- ✅ Signed copy linked to IEP record
- ✅ Guidance can review signed copy
- ✅ Guidance can approve or request revisions
- ✅ Principal can view signed copy after Guidance approval
- ✅ Principal can approve/reject
- ✅ Rejection requires reason
- ✅ Learner status updates to 'active' on approval
- ✅ All parties receive email notifications
- ✅ Complete audit trail maintained
- ✅ Active IEP is read-only
- ✅ Status flow indicator shows current stage
- ✅ UI consistent with system design

---

## Dugang nga Features

### Progress Tracking

**Unsa akong gusto mahitabo:**
<!-- I-add diri imong requirements -->

**Logic/Rules:**
<!-- I-add diri imong requirements -->

**Functions nga kinahanglan:**
<!-- I-add diri imong requirements -->

**UI/UX:**
<!-- I-add diri imong requirements -->

---

### Notifications

**Unsa akong gusto mahitabo:**

### **Comprehensive Notification System**

**Overview:**
- Email notifications for all major workflow events
- In-app notifications (dashboard badges)
- Notification preferences per user
- Notification history/log
- Mark as read/unread functionality

**Notification Types:**

**1. Enrollment Notifications**
- Parent submits BEEF → SPED receives notification
- Parent uploads documents → SPED receives notification
- SPED approves enrollment → Parent receives email with LRN
- SPED rejects enrollment → Parent receives email with reason

**2. Assessment Notifications**
- Enrollment approved → Parent receives "Assessment Unlocked" notification
- Parent submits assessment → SPED receives notification
- SPED reviews assessment → Parent receives confirmation

**3. IEP Draft Notifications**
- SPED sends IEP draft → Guidance, Principal, Parent receive notifications
- Guidance adds feedback → SPED receives notification
- Principal reviews draft → SPED receives notification

**4. Meeting Notifications**
- Meeting scheduled → All participants receive invitations
- Participant confirms → Organizer receives notification
- Participant declines → Organizer receives notification
- Meeting reminder (3 days before) → All participants
- Meeting reminder (1 day before) → All participants
- Meeting reminder (1 hour before) → All participants
- Meeting completed → All participants receive summary

**5. IEP Finalization Notifications**
- IEP finalized → Guidance, Principal receive notifications
- Signed IEP uploaded → Guidance receives notification
- Guidance approves → Principal receives notification
- Guidance requests revisions → SPED receives notification
- Principal approves → Parent, SPED, Guidance receive notifications
- Principal rejects → SPED receives notification with reason

**6. System Notifications**
- Password change → User receives confirmation
- Account created → User receives credentials
- Role changed → User receives notification
- Important announcements → All users

**Logic/Rules:**
- Email notifications sent immediately
- In-app notifications appear in dashboard
- Notification badge shows unread count
- Users can mark notifications as read
- Notification history kept for 90 days
- Users can set notification preferences:
  - Email only
  - In-app only
  - Both
  - None (for non-critical notifications)
- Critical notifications always sent (cannot disable)
- Email templates professional and consistent
- All emails include system logo and branding

**Functions nga kinahanglan:**
- `sendNotification(userId, type, data)` - Sends notification
- `sendEmailNotification(email, subject, body, data)` - Sends email
- `createInAppNotification(userId, message, link)` - Creates dashboard notification
- `getUnreadNotifications(userId)` - Gets unread count
- `markAsRead(notificationId)` - Marks notification as read
- `markAllAsRead(userId)` - Marks all as read
- `getNotificationHistory(userId, limit)` - Gets notification history
- `deleteNotification(notificationId)` - Deletes notification
- `getUserNotificationPreferences(userId)` - Gets user preferences
- `updateNotificationPreferences(userId, preferences)` - Updates preferences
- `sendBulkNotifications(userIds, type, data)` - Sends to multiple users
- `scheduleReminder(meetingId, reminderType)` - Schedules meeting reminders

**UI/UX:**
- **Consistent sa system design** (Bootstrap 5, maroon/bluish theme)

**Notification Bell Icon (Header):**
- Bell icon with badge showing unread count
- Click to open notification dropdown
- Dropdown shows recent 5 notifications
- "View All" link to full notification page
- "Mark All as Read" button

**Notification Dropdown:**
```
┌─────────────────────────────────────┐
│  Notifications (3)                  │
│  [Mark All as Read]                 │
├─────────────────────────────────────┤
│  🔴 New assessment submitted        │
│     Ronald Martinez - 2 hours ago   │
│     [View]                          │
├─────────────────────────────────────┤
│  🟡 Meeting scheduled                │
│     May 5, 2026 at 2:00 PM          │
│     [View Details]                  │
├─────────────────────────────────────┤
│  🟢 IEP approved                     │
│     Allysa Canonizado - 1 day ago   │
│     [View IEP]                      │
├─────────────────────────────────────┤
│  [View All Notifications]           │
└─────────────────────────────────────┘
```

**Notification Page:**
- Full list of notifications
- Filter by type (all, enrollment, assessment, IEP, meeting, system)
- Filter by status (all, unread, read)
- Search notifications
- Bulk actions (mark all as read, delete all read)
- Pagination

**Notification Preferences Page:**
- Toggle switches for each notification type
- Email/In-app/Both/None options
- "Save Preferences" button
- Note: Critical notifications cannot be disabled

**Email Template Structure:**
```
┌─────────────────────────────────────┐
│  [SignED SPED Logo]                 │
├─────────────────────────────────────┤
│                                     │
│  [Notification Title]               │
│                                     │
│  [Notification Body]                │
│                                     │
│  [Action Button]                    │
│                                     │
│  [Additional Details]               │
│                                     │
├─────────────────────────────────────┤
│  SignED SPED System                 │
│  [School Name]                      │
│  [Contact Information]              │
└─────────────────────────────────────┘
```

**Database Structure:**
```sql
-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL, -- enrollment, assessment, iep, meeting, system
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) NULL, -- Link to relevant page
    data JSON NULL, -- Additional data
    is_read BOOLEAN DEFAULT FALSE,
    is_critical BOOLEAN DEFAULT FALSE, -- Cannot be disabled
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_unread (user_id, is_read),
    INDEX idx_created (created_at)
);

-- Notification preferences
CREATE TABLE IF NOT EXISTS notification_preferences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    enrollment_email BOOLEAN DEFAULT TRUE,
    enrollment_inapp BOOLEAN DEFAULT TRUE,
    assessment_email BOOLEAN DEFAULT TRUE,
    assessment_inapp BOOLEAN DEFAULT TRUE,
    iep_email BOOLEAN DEFAULT TRUE,
    iep_inapp BOOLEAN DEFAULT TRUE,
    meeting_email BOOLEAN DEFAULT TRUE,
    meeting_inapp BOOLEAN DEFAULT TRUE,
    system_email BOOLEAN DEFAULT TRUE,
    system_inapp BOOLEAN DEFAULT TRUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Email log (for tracking sent emails)
CREATE TABLE IF NOT EXISTS email_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recipient_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    error_message TEXT NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_sent (sent_at)
);
```

**Example Notification Scenarios:**

**Scenario 1: Enrollment Approved**
```
Email to Parent:
Subject: Enrollment Approved - Learner Account Created
Body: [See Step 3 email template]
In-app: "Enrollment approved for Allysa Canonizado. LRN: 202604123456"
```

**Scenario 2: Meeting Scheduled**
```
Email to All Participants:
Subject: IEP Meeting Invitation - Allysa Canonizado
Body: [See Step 6 email template]
In-app: "IEP meeting scheduled for May 5, 2026 at 2:00 PM"
```

**Scenario 3: IEP Approved**
```
Email to Parent:
Subject: IEP Approved - Allysa Canonizado
Body: [See Step 7 email template]
In-app: "IEP approved! Your child is now active in the system."

Email to SPED Teacher:
Subject: IEP Approved - Allysa Canonizado
Body: [See Step 7 email template]
In-app: "IEP approved by Principal. You can now implement the IEP."
```

**Success Criteria:**
- ✅ Email notifications sent for all major events
- ✅ In-app notifications appear in dashboard
- ✅ Notification badge shows unread count
- ✅ Users can mark as read/unread
- ✅ Users can view notification history
- ✅ Users can set notification preferences
- ✅ Critical notifications always sent
- ✅ Email templates professional and branded
- ✅ Meeting reminders sent automatically (3 days, 1 day, 1 hour)
- ✅ Notification system scalable and performant
- ✅ UI consistent with system design

---

### Reports

**Unsa akong gusto mahitabo:**
<!-- I-add diri imong requirements -->

**Logic/Rules:**
<!-- I-add diri imong requirements -->

**Functions nga kinahanglan:**
<!-- I-add diri imong requirements -->

**UI/UX:**
<!-- I-add diri imong requirements -->

---

## Mga Notes ug Questions

<!-- I-add diri ang imong questions o clarifications -->

---

## Priority Order

Pag na-fill out na nimo, i-implement nato sunod ani nga order:

1. **High Priority** (Para mahuman ang core workflow)
   - [ ] Step 4: Assessment
   - [ ] Step 5: IEP Creation
   - [ ] Step 6: IEP Meeting
   - [ ] Step 7: IEP Approval

2. **Medium Priority** (Para mas maayo ang functionality)
   - [ ] Progress Tracking
   - [ ] Notifications
   - [ ] Reports

3. **Low Priority** (Nice to have)
   - [ ] Advanced features
   - [ ] Optimizations
   - [ ] Dugang nga reports

---

## Unsaon Pag-fill Out

1. **Usa ra ka step sa usa ka higayon** - Ayaw tanan kausa
2. **Specific jud** - Dili lang "make it work", kondili "pag mu-click ang user ug X, ang system kinahanglan mu-buhat ug Y"
3. **I-include ang examples** - Ipakita nako ang konkretong scenario
4. **Hunahunaa ang edge cases** - Unsa kung naa'y sayop?
5. **Hunahunaa ang user** - Parent, SPED teacher, Principal - unsa ilang kinahanglan makita/buhaton?

**Example sa maayong requirement:**

```
### Step 4: Assessment - Part A Completion

**Unsa akong gusto mahitabo:**
- Pag nahuman na sa SPED teacher ang tanan nga Part A sections, mu-appear ang "Complete Assessment" button
- Pag mu-click, mu-show ug confirmation dialog
- Pag na-confirm, ang assessment ma-mark as complete ug ang learner status mu-update

**Logic/Rules:**
- Kinahanglan tanan nga 5 ka Part A sections naa'y data (academic, social, communication, motor, self-help)
- Ang Part B optional ra ug dili mu-block sa completion
- Pag na-complete na, ang assessment mahimong read-only
- Ang learner status mu-change gikan 'enrolled' to 'assessment_complete'

**Functions nga kinahanglan:**
- validatePartA(assessmentData) - mu-return ug true kung tanan nga sections na-fill
- completeAssessment(assessmentId) - mu-mark as complete, mu-update sa learner status
- isAssessmentComplete(assessmentId) - mu-check kung na-complete na ba

**UI/UX:**
- Progress bar nga mu-show 5/5 sections completed
- Green nga "Complete Assessment" button (disabled until tanan sections human na)
- Confirmation modal: "Sigurado ka ba? Dili na ni ma-undo."
- Success message: "Assessment completed! Pwede na ka mu-create ug IEP."

**Example Flow:**
1. Teacher mu-fill sa academic skills section → progress 1/5
2. Teacher mu-fill sa social skills section → progress 2/5
3. Teacher mu-fill sa communication section → progress 3/5
4. Teacher mu-fill sa motor skills section → progress 4/5
5. Teacher mu-fill sa self-help section → progress 5/5
6. Ang "Complete Assessment" button mu-enable
7. Teacher mu-click sa button → confirmation dialog
8. Teacher mu-confirm → assessment saved, learner status updated
9. Mu-redirect sa IEP creation page
```

---

## Ready na ka?

I-send lang nimo ang imong requirements para sa matag step, ug ako na ang:
1. ✅ Mu-add sa document
2. ✅ Mu-update sa main requirements.md
3. ✅ Mu-create ug detailed design specifications
4. ✅ Mu-generate ug implementation tasks
5. ✅ Mu-implement sa tanan in one go!

**I-send lang nimo imong requirements ani nga format, step by step!** 😊

**Pwede pud nimo i-send tanan kausa o usa-usa lang!**


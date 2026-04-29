# SignED SPED System - Complete Workflow Specification

## Overview
This document outlines the complete 7-step SPED (Special Education) workflow from initial enrollment to active learner status. Each step is documented with current status, required functions, and implementation details.

---

## Workflow Steps Overview

```
Step 1: Parent Enrollment (BEEF Form)
   ↓
Step 2: Document Upload (PSA, PWD ID, Medical Records)
   ↓
Step 3: SPED Verification & Approval
   ↓
Step 4: Assessment (Part A & Optional Part B)
   ↓
Step 5: IEP Creation
   ↓
Step 6: IEP Meeting
   ↓
Step 7: IEP Approval → Active Learner
```

---

## Step 1: Parent Enrollment (BEEF Form)

### Purpose
Parents fill out the Basic Education Enrollment Form (BEEF) with complete learner information.

### Current Status: ✅ **WORKING**

### What's Working
- ✅ BEEF form interface (`/enrollment/beef`)
- ✅ Form validation
- ✅ Data storage in `enrollments.beef_data` (JSON)
- ✅ Creates enrollment record with status `pending_documents`
- ✅ Redirects to document upload page

### Database Tables
- **enrollments**: Stores enrollment data
  - `beef_data` (JSON): Complete BEEF form data
  - `status`: 'pending_documents'
  - `parent_id`: Links to parent user

### Key Functions
| Function | Location | Status |
|----------|----------|--------|
| `beef()` | EnrollmentController | ✅ Working |
| `handleBeefSubmission()` | EnrollmentController | ✅ Working |
| `createWithBeef()` | Enrollment Model | ✅ Working |

### Data Collected
- Learner personal info (name, DOB, gender, place of birth)
- Address (current and permanent)
- Parent/Guardian info
- Educational background
- Special needs information
- 4Ps beneficiary status
- Indigenous people status
- Learning modalities

---

## Step 2: Document Upload

### Purpose
Parents upload required documents to support the enrollment application.

### Current Status: ✅ **WORKING**

### What's Working
- ✅ Upload interface (`/enrollment/upload`)
- ✅ Document validation and encryption
- ✅ Storage in `enrollment_documents` table
- ✅ BEEF marked as "Already Submitted"
- ✅ PSA Birth Certificate (Required)
- ✅ PWD ID Card (Optional)
- ✅ Medical Records (Optional)
- ✅ Status updates to `pending_verification` when PSA uploaded

### Database Tables
- **enrollment_documents**: Stores document metadata
  - `document_type`: 'psa', 'pwd_id', 'medical_record', 'beef'
  - `encrypted_filename`: Secure storage reference
  - `enrollment_id`: Links to enrollment
- **document_store**: Encrypted document storage

### Key Functions
| Function | Location | Status |
|----------|----------|--------|
| `upload()` | EnrollmentController | ✅ Working |
| `handleFileUpload()` | EnrollmentController | ✅ Working |
| `uploadDocument()` | Enrollment Model | ✅ Working |
| `hasAllDocuments()` | Enrollment Model | ✅ Working |
| `store()` | DocumentStore Model | ✅ Working |

### Document Requirements
- **Required**: PSA Birth Certificate only
- **Optional**: PWD ID, Medical Records
- **Auto-submitted**: BEEF form (from Step 1)

---

## Step 3: SPED Verification & Approval

### Purpose
SPED teachers review enrollment applications and approve/reject them.

### Current Status: ✅ **WORKING**

### What's Working
- ✅ Verification interface (`/enrollment/verify`)
- ✅ Review page (`/enrollment/viewEnrollment`)
- ✅ Document viewing (inline in new tab)
- ✅ Approve function creates learner record
- ✅ Reject function with reason
- ✅ Email notifications
- ✅ Status updates to `approved` or `rejected`

### Database Tables
- **enrollments**: Status updated
  - `status`: 'approved' or 'rejected'
  - `verified_by`: SPED teacher ID
  - `verified_at`: Timestamp
  - `rejection_reason`: If rejected
- **learners**: New record created on approval
  - Extracts data from `beef_data` JSON
  - Creates user account for learner
  - Status: 'enrolled'

### Key Functions
| Function | Location | Status |
|----------|----------|--------|
| `verify()` | EnrollmentController | ✅ Working |
| `viewEnrollment()` | EnrollmentController | ✅ Working |
| `viewDocument()` | EnrollmentController | ✅ Working |
| `approve()` | EnrollmentController | ✅ Working |
| `reject()` | EnrollmentController | ✅ Working |
| `createFromEnrollment()` | Learner Model | ✅ Working (Fixed) |
| `sendApprovalEmail()` | EnrollmentController | ✅ Working |
| `sendRejectionEmail()` | EnrollmentController | ✅ Working |

### Approval Process
1. SPED teacher reviews documents
2. Clicks "Approve" button
3. System creates:
   - User account for learner
   - Learner record with complete data from BEEF
4. Sends approval email to parent
5. Learner status: 'enrolled'

---

## Step 4: Assessment

### Purpose
SPED teachers conduct assessments to determine learner's needs and capabilities.

### Current Status: ⚠️ **PARTIALLY IMPLEMENTED**

### What's Working
- ✅ Assessment list page (`/assessment/list`)
- ✅ Assessment creation interface
- ✅ Part A assessment (required)
- ✅ Part B assessment (optional)
- ✅ Assessment data storage

### What Needs Work
- ⚠️ Assessment form validation
- ⚠️ Assessment completion workflow
- ⚠️ Status transition to 'assessment_complete'
- ⚠️ Integration with IEP creation

### Database Tables
- **assessments**: Stores assessment data
  - `learner_id`: Links to learner
  - `assessed_by`: SPED teacher ID
  - `part_a_data` (JSON): Required assessment
  - `part_b_data` (JSON): Optional assessment
  - `status`: 'pending', 'in_progress', 'completed'

### Key Functions
| Function | Location | Status |
|----------|----------|--------|
| `list()` | AssessmentController | ✅ Working |
| `create()` | AssessmentController | ⚠️ Needs review |
| `save()` | AssessmentController | ⚠️ Needs review |
| `getByLearner()` | Assessment Model | ✅ Working |

### Assessment Components
- **Part A (Required)**:
  - Academic skills
  - Social skills
  - Communication skills
  - Motor skills
  - Self-help skills
  
- **Part B (Optional)**:
  - Detailed behavioral assessment
  - Specific learning needs
  - Recommended interventions

### Status Transitions
- Learner status: 'enrolled' → 'assessment_pending' → 'assessment_complete'

---

## Step 5: IEP Creation

### Purpose
Create Individualized Education Program based on assessment results.

### Current Status: ⚠️ **PARTIALLY IMPLEMENTED**

### What's Working
- ✅ IEP list page (`/iep/list`)
- ✅ IEP creation interface
- ✅ IEP data storage
- ✅ Link to assessment data

### What Needs Work
- ⚠️ IEP form completion workflow
- ⚠️ Goals and objectives management
- ⚠️ Accommodation tracking
- ⚠️ Status transitions
- ⚠️ Integration with meeting scheduling

### Database Tables
- **ieps**: Stores IEP data
  - `learner_id`: Links to learner
  - `assessment_id`: Links to assessment
  - `created_by`: SPED teacher ID
  - `goals` (JSON): Educational goals
  - `accommodations` (JSON): Required accommodations
  - `status`: 'draft', 'pending_meeting', 'pending_approval', 'approved', 'active'

### Key Functions
| Function | Location | Status |
|----------|----------|--------|
| `list()` | IepController | ✅ Working |
| `create()` | IepController | ⚠️ Needs review |
| `save()` | IepController | ⚠️ Needs review |
| `getByLearner()` | Iep Model | ✅ Working |
| `getCurrentForLearner()` | Iep Model | ✅ Working |

### IEP Components
- Present levels of performance
- Annual goals
- Short-term objectives
- Special education services
- Related services
- Accommodations and modifications
- Assessment accommodations
- Transition services (if applicable)

### Status Transitions
- IEP status: 'draft' → 'pending_meeting'
- Learner status: 'assessment_complete' → 'iep_meeting_scheduled'

---

## Step 6: IEP Meeting

### Purpose
Conduct IEP meeting with parents, teachers, and relevant staff to discuss and finalize the IEP.

### Current Status: ⚠️ **PARTIALLY IMPLEMENTED**

### What's Working
- ✅ Meeting list page (`/iep/meetings`)
- ✅ Meeting scheduling interface
- ✅ Meeting data storage
- ✅ Participant tracking

### What Needs Work
- ⚠️ Meeting scheduling workflow
- ⚠️ Attendance confirmation
- ⚠️ Meeting notes/minutes
- ⚠️ Parent notification system
- ⚠️ Status transitions after meeting

### Database Tables
- **iep_meetings**: Stores meeting data
  - `iep_id`: Links to IEP
  - `scheduled_date`: Meeting date/time
  - `location`: Meeting location
  - `participants` (JSON): List of attendees
  - `attendance_confirmed` (JSON): Who confirmed
  - `meeting_notes`: Discussion notes
  - `status`: 'scheduled', 'completed', 'cancelled'

### Key Functions
| Function | Location | Status |
|----------|----------|--------|
| `meetings()` | IepController | ✅ Working |
| `scheduleMeeting()` | IepController | ⚠️ Needs review |
| `confirmAttendance()` | IepController | ⚠️ Needs review |
| `recordMeeting()` | IepController | ⚠️ Needs review |
| `getUpcoming()` | IepMeeting Model | ✅ Working |

### Meeting Participants
- Parent/Guardian (required)
- SPED Teacher (required)
- General Education Teacher
- School Administrator
- Related Service Providers
- Student (if appropriate)

### Status Transitions
- Meeting status: 'scheduled' → 'completed'
- IEP status: 'pending_meeting' → 'pending_approval'
- Learner status: 'iep_meeting_scheduled' → 'iep_meeting_complete'

---

## Step 7: IEP Approval & Active Status

### Purpose
Principal/Administrator approves the IEP, making the learner officially active in the SPED program.

### Current Status: ⚠️ **PARTIALLY IMPLEMENTED**

### What's Working
- ✅ Approval interface for principals
- ✅ IEP approval function
- ✅ Status updates

### What Needs Work
- ⚠️ Approval workflow
- ⚠️ Rejection with feedback
- ⚠️ Final status transition to 'active'
- ⚠️ Notification to all parties
- ⚠️ IEP activation

### Database Tables
- **ieps**: Status updated
  - `status`: 'approved' or 'rejected'
  - `approved_by`: Principal ID
  - `approved_at`: Timestamp
  - `rejection_reason`: If rejected
- **learners**: Status updated
  - `status`: 'active'

### Key Functions
| Function | Location | Status |
|----------|----------|--------|
| `approvals()` | IepController | ⚠️ Needs review |
| `approve()` | IepController | ⚠️ Needs review |
| `reject()` | IepController | ⚠️ Needs review |
| `updateStatus()` | Iep Model | ✅ Working |
| `updateStatus()` | Learner Model | ✅ Working |

### Approval Process
1. Principal reviews IEP
2. Approves or rejects with reason
3. If approved:
   - IEP status → 'approved'
   - Learner status → 'active'
   - Notifications sent
4. If rejected:
   - IEP status → 'rejected'
   - Returns to SPED teacher for revision

### Final Status
- **IEP**: 'approved' and 'active'
- **Learner**: 'active'
- **Ready for**: Learning materials, progress tracking, ongoing support

---

## Additional Features

### Student Records
**Status**: ✅ **WORKING**

- View all enrolled/active learners
- Search and filter capabilities
- Quick access to learner profiles
- Links to assessments, IEPs, and materials

### Learning Materials
**Status**: ⚠️ **PARTIALLY IMPLEMENTED**

- Upload materials for learners
- Assign materials to specific learners
- Track material access and completion
- Learner submission of work

### Progress Tracking
**Status**: ❌ **NOT IMPLEMENTED**

- Track IEP goal progress
- Regular progress reports
- Parent communication
- Data visualization

---

## Summary: What's Working vs What Needs Work

### ✅ Fully Working (Steps 1-3)
1. **Parent Enrollment** - BEEF form submission
2. **Document Upload** - PSA, PWD ID, Medical Records
3. **SPED Verification** - Review, approve/reject, create learner

### ⚠️ Partially Working (Steps 4-7)
4. **Assessment** - Basic structure exists, needs workflow completion
5. **IEP Creation** - Basic structure exists, needs form completion
6. **IEP Meeting** - Basic structure exists, needs scheduling workflow
7. **IEP Approval** - Basic structure exists, needs approval workflow

### ❌ Missing Features
- Complete assessment workflow
- IEP goals/objectives management
- Meeting scheduling and notifications
- Approval workflow with notifications
- Progress tracking system
- Reporting and analytics

---

## Next Steps Priority

### High Priority (Complete Core Workflow)
1. ✅ Fix learner creation (DONE)
2. ⚠️ Complete assessment workflow
3. ⚠️ Complete IEP creation workflow
4. ⚠️ Complete meeting scheduling
5. ⚠️ Complete approval workflow

### Medium Priority (Enhance Functionality)
6. Add progress tracking
7. Improve notifications
8. Add reporting features
9. Enhance search/filter capabilities

### Low Priority (Nice to Have)
10. Data visualization
11. Export/print features
12. Bulk operations
13. Advanced analytics

---

## Technical Debt & Issues

### Current Issues
1. ✅ Learner creation missing middle_name/suffix (FIXED)
2. ⚠️ Assessment form needs validation
3. ⚠️ IEP form needs completion workflow
4. ⚠️ Meeting notifications not implemented
5. ⚠️ Approval workflow incomplete

### Code Quality
- Need consistent error handling
- Need better logging
- Need comprehensive testing
- Need code documentation

---

## Database Schema Status

### ✅ Complete Tables
- users
- enrollments
- enrollment_documents
- document_store
- learners
- audit_logs

### ⚠️ Needs Review
- assessments (structure OK, workflow incomplete)
- ieps (structure OK, workflow incomplete)
- iep_meetings (structure OK, workflow incomplete)

### ❌ Missing Tables
- progress_reports
- goal_tracking
- notifications (partially implemented)

---

## Conclusion

The SignED SPED system has a solid foundation with Steps 1-3 fully functional. The remaining steps (4-7) have the basic structure in place but need workflow completion and integration. The priority should be completing the core workflow before adding advanced features.

**Current Completion**: ~40%
**Target for MVP**: Complete Steps 1-7 (core workflow)
**Estimated Work**: 2-3 weeks for core workflow completion

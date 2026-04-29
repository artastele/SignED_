-- ============================================
-- DATABASE UPDATE: Complete IEP Workflow
-- Document Upload, Meeting Scheduling, Signatures
-- ============================================

-- Add columns to ieps table for document management and finalization
ALTER TABLE ieps 
ADD COLUMN draft_document_id INT NULL AFTER draft_data COMMENT 'Link to uploaded IEP draft document';

ALTER TABLE ieps 
ADD COLUMN finalized_data LONGTEXT NULL AFTER draft_document_id COMMENT 'Complete IEP data after meeting (IEP P3)';

ALTER TABLE ieps 
ADD COLUMN signed_document_id INT NULL AFTER finalized_data COMMENT 'Link to signed IEP document';

ALTER TABLE ieps 
ADD COLUMN guidance_reviewed TINYINT(1) DEFAULT 0 AFTER meeting_scheduled;

ALTER TABLE ieps 
ADD COLUMN guidance_reviewed_at TIMESTAMP NULL AFTER guidance_reviewed;

ALTER TABLE ieps 
ADD COLUMN guidance_reviewed_by INT NULL AFTER guidance_reviewed_at;

ALTER TABLE ieps 
ADD COLUMN guidance_feedback TEXT NULL AFTER guidance_reviewed_by;

-- Add foreign keys
ALTER TABLE ieps 
ADD FOREIGN KEY (draft_document_id) REFERENCES document_store(id) ON DELETE SET NULL;

ALTER TABLE ieps 
ADD FOREIGN KEY (signed_document_id) REFERENCES document_store(id) ON DELETE SET NULL;

ALTER TABLE ieps 
ADD FOREIGN KEY (guidance_reviewed_by) REFERENCES users(id) ON DELETE SET NULL;

-- Update ieps status enum to include new statuses
ALTER TABLE ieps 
MODIFY COLUMN status ENUM(
    'draft', 
    'pending_upload', 
    'pending_meeting', 
    'meeting_scheduled',
    'meeting_completed',
    'pending_finalization',
    'pending_signatures',
    'pending_guidance_review',
    'pending_approval', 
    'approved', 
    'active', 
    'rejected'
) DEFAULT 'draft';

-- IEP Meetings table - Add more columns for complete workflow
ALTER TABLE iep_meetings 
ADD COLUMN meeting_notes TEXT NULL AFTER agenda COMMENT 'Notes recorded during meeting';

ALTER TABLE iep_meetings 
ADD COLUMN decisions TEXT NULL AFTER meeting_notes COMMENT 'Decisions made during meeting';

ALTER TABLE iep_meetings 
ADD COLUMN completed_at TIMESTAMP NULL AFTER status;

ALTER TABLE iep_meetings 
ADD COLUMN completed_by INT NULL AFTER completed_at;

ALTER TABLE iep_meetings 
ADD COLUMN cancellation_reason TEXT NULL AFTER completed_by;

ALTER TABLE iep_meetings 
ADD COLUMN reschedule_reason TEXT NULL AFTER cancellation_reason;

-- Add foreign key
ALTER TABLE iep_meetings 
ADD FOREIGN KEY (completed_by) REFERENCES users(id) ON DELETE SET NULL;

-- Update meeting status enum
ALTER TABLE iep_meetings 
MODIFY COLUMN status ENUM(
    'scheduled', 
    'confirmed', 
    'completed', 
    'cancelled',
    'rescheduled'
) DEFAULT 'scheduled';

-- IEP Meeting Participants table (for invitations and confirmations)
CREATE TABLE IF NOT EXISTS iep_meeting_participants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    meeting_id INT NOT NULL,
    user_id INT NULL,
    participant_type ENUM('parent', 'sped_teacher', 'guidance', 'gen_ed_teacher', 'principal', 'specialist', 'other') NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL,
    is_required TINYINT(1) DEFAULT 0 COMMENT '1 = required participant, 0 = optional',
    invitation_status ENUM('pending', 'confirmed', 'declined') DEFAULT 'pending',
    confirmed_at TIMESTAMP NULL,
    decline_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (meeting_id) REFERENCES iep_meetings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_meeting_id (meeting_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- IEP Feedback table (for Guidance and Principal feedback)
CREATE TABLE IF NOT EXISTS iep_feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NOT NULL,
    user_id INT NOT NULL,
    user_role ENUM('guidance', 'principal', 'sped_teacher', 'other') NOT NULL,
    feedback_type ENUM('draft_review', 'meeting_feedback', 'final_review') NOT NULL,
    feedback TEXT NOT NULL,
    status ENUM('pending', 'addressed', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (iep_id) REFERENCES ieps(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_iep_id (iep_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- IEP Step Objectives table (for IEP P3 - detailed lesson planning)
CREATE TABLE IF NOT EXISTS iep_step_objectives (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NOT NULL,
    step_number INT NOT NULL,
    step_objective TEXT NOT NULL,
    plan_of_activities TEXT NULL,
    materials TEXT NULL,
    duration_of_lp VARCHAR(100) NULL COMMENT 'Duration of Lesson Plan',
    instructional_evaluation TEXT NULL,
    observation TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (iep_id) REFERENCES ieps(id) ON DELETE CASCADE,
    INDEX idx_iep_id (iep_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- IEP Signatures table (for tracking all required signatures)
CREATE TABLE IF NOT EXISTS iep_signatures (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NOT NULL,
    signer_type ENUM('parent', 'sped_teacher', 'guidance', 'gen_ed_teacher', 'principal', 'ilrc_supervisor') NOT NULL,
    signer_name VARCHAR(255) NOT NULL,
    user_id INT NULL,
    signature_data TEXT NULL COMMENT 'Digital signature or scanned signature path',
    signed_at TIMESTAMP NULL,
    is_required TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (iep_id) REFERENCES ieps(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_iep_id (iep_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- IEP Notifications table (for tracking who was notified)
CREATE TABLE IF NOT EXISTS iep_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NOT NULL,
    user_id INT NOT NULL,
    notification_type ENUM('draft_shared', 'meeting_scheduled', 'meeting_reminder', 'meeting_cancelled', 'meeting_rescheduled', 'feedback_requested', 'approval_requested', 'iep_approved', 'iep_rejected') NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at TIMESTAMP NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (iep_id) REFERENCES ieps(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_iep_id (iep_id),
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add columns to learners table for IEP tracking
ALTER TABLE learners 
ADD COLUMN iep_status ENUM('none', 'assessment_pending', 'iep_draft', 'iep_meeting_scheduled', 'iep_meeting_complete', 'iep_pending_approval', 'iep_active') DEFAULT 'none' AFTER status;

-- ============================================
-- VERIFICATION QUERIES:
-- ============================================
-- DESCRIBE ieps;
-- DESCRIBE iep_meetings;
-- DESCRIBE iep_meeting_participants;
-- DESCRIBE iep_feedback;
-- DESCRIBE iep_step_objectives;
-- DESCRIBE iep_signatures;
-- DESCRIBE iep_notifications;
-- DESCRIBE learners;

-- ============================================
-- SAMPLE DATA QUERIES:
-- ============================================
-- SELECT * FROM iep_meeting_participants WHERE meeting_id = 1;
-- SELECT * FROM iep_feedback WHERE iep_id = 1;
-- SELECT * FROM iep_notifications WHERE user_id = 1 AND is_read = 0;

-- ============================================
-- NOTES:
-- ============================================
-- Workflow Status Flow:
-- 1. draft -> SPED creates IEP draft
-- 2. pending_upload -> SPED clicks "Send IEP", needs to upload document
-- 3. pending_meeting -> Document uploaded, notifications sent, ready to schedule
-- 4. meeting_scheduled -> Meeting scheduled with participants
-- 5. meeting_completed -> Meeting done, notes recorded
-- 6. pending_finalization -> SPED finalizes IEP (IEP P3 format)
-- 7. pending_signatures -> Print, sign, upload signed document
-- 8. pending_guidance_review -> Guidance reviews signed document
-- 9. pending_approval -> Principal reviews and approves
-- 10. approved -> IEP approved
-- 11. active -> IEP is now active and being implemented

-- Required Participants:
-- - Parent/Guardian (required)
-- - SPED Teacher (required)
-- - Guidance Counselor (required)
-- - Principal/Administrator (required)
-- - General Ed Teacher (optional)
-- - Other specialists (optional)

-- Minimum 3 days notice before meeting
-- Parent can decline = must reschedule
-- All required participants must confirm


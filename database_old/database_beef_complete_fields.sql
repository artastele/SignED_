-- Add Complete BEEF Form Fields to Enrollments Table
-- This migration adds all necessary fields to store complete BEEF form data

USE signed_system;

-- Add BEEF-related columns to enrollments table
ALTER TABLE enrollments
ADD COLUMN beef_data JSON NULL COMMENT 'Complete BEEF form data as JSON' AFTER learner_grade,
ADD COLUMN is_returning_student TINYINT(1) DEFAULT 0 COMMENT 'Is this a returning/old student?' AFTER beef_data,
ADD COLUMN previous_lrn VARCHAR(12) NULL COMMENT 'Previous LRN for returning students' AFTER is_returning_student,
ADD COLUMN parent_contact_number VARCHAR(20) NULL COMMENT 'Parent contact number' AFTER previous_lrn,
ADD COLUMN parent_address TEXT NULL COMMENT 'Parent address' AFTER parent_contact_number;

-- Add LRN field to learners table if not exists
ALTER TABLE learners
ADD COLUMN lrn VARCHAR(12) UNIQUE NULL COMMENT 'Learner Reference Number' AFTER id;

-- Add index for LRN lookup
CREATE INDEX idx_lrn ON learners(lrn);
CREATE INDEX idx_previous_lrn ON enrollments(previous_lrn);

-- Display completion message
SELECT 'BEEF Complete Fields Migration Complete!' as Status;
SELECT 'Added: beef_data, is_returning_student, previous_lrn, parent_contact_number, parent_address' as Summary;

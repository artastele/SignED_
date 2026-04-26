-- Add 'pending_info' status to learners table for Google Sign In users
-- This status is used when a user signs in with Google and selects 'learner' role
-- but hasn't provided complete learner information yet

ALTER TABLE learners 
MODIFY COLUMN status ENUM(
    'pending_info',
    'enrolled', 
    'assessment_pending', 
    'assessment_complete', 
    'iep_meeting_scheduled', 
    'iep_meeting_complete', 
    'iep_pending_approval', 
    'iep_approved', 
    'active'
) NOT NULL DEFAULT 'enrolled';

-- Also allow NULL values for fields that may not be available during Google Sign In
ALTER TABLE learners 
MODIFY COLUMN parent_id INT NULL,
MODIFY COLUMN date_of_birth DATE NULL,
MODIFY COLUMN grade_level VARCHAR(10) NULL;

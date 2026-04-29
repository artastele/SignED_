-- Check current database structure
-- Run these queries one by one in phpMyAdmin

-- Check ieps table structure
DESCRIBE ieps;

-- Check learners table structure
DESCRIBE learners;

-- Check assessments table structure
DESCRIBE assessments;

-- Check if IEP-related tables exist
SHOW TABLES LIKE 'iep_%';

-- Check enrollment_history table
SHOW TABLES LIKE 'enrollment_history';

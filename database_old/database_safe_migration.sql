-- Safe Database Migration Script
-- This script can be run multiple times without errors
-- It checks if columns/tables exist before creating them

USE signed_system;

-- ============================================
-- 1. SAFELY ADD COLUMNS TO USERS TABLE
-- ============================================

-- Add phone column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'users' 
AND COLUMN_NAME = 'phone';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL AFTER email', 
    'SELECT "✓ Column phone already exists" AS Status');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add address column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'users' 
AND COLUMN_NAME = 'address';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN address TEXT NULL AFTER phone', 
    'SELECT "✓ Column address already exists" AS Status');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add emergency_contact column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'users' 
AND COLUMN_NAME = 'emergency_contact';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN emergency_contact VARCHAR(100) NULL AFTER address', 
    'SELECT "✓ Column emergency_contact already exists" AS Status');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add emergency_phone column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'users' 
AND COLUMN_NAME = 'emergency_phone';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN emergency_phone VARCHAR(20) NULL AFTER emergency_contact', 
    'SELECT "✓ Column emergency_phone already exists" AS Status');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- 2. SAFELY ADD BEEF COLUMNS TO ENROLLMENTS TABLE
-- ============================================

-- Add beef_data column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'enrollments' 
AND COLUMN_NAME = 'beef_data';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE enrollments ADD COLUMN beef_data JSON NULL COMMENT "Complete BEEF form data as JSON" AFTER learner_grade', 
    'SELECT "✓ Column beef_data already exists" AS Status');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add is_returning_student column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'enrollments' 
AND COLUMN_NAME = 'is_returning_student';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE enrollments ADD COLUMN is_returning_student TINYINT(1) DEFAULT 0 COMMENT "Is this a returning/old student?" AFTER beef_data', 
    'SELECT "✓ Column is_returning_student already exists" AS Status');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add previous_lrn column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'enrollments' 
AND COLUMN_NAME = 'previous_lrn';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE enrollments ADD COLUMN previous_lrn VARCHAR(12) NULL COMMENT "Previous LRN for returning students" AFTER is_returning_student', 
    'SELECT "✓ Column previous_lrn already exists" AS Status');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add parent_contact_number column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'enrollments' 
AND COLUMN_NAME = 'parent_contact_number';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE enrollments ADD COLUMN parent_contact_number VARCHAR(20) NULL COMMENT "Parent contact number" AFTER previous_lrn', 
    'SELECT "✓ Column parent_contact_number already exists" AS Status');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add parent_address column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'enrollments' 
AND COLUMN_NAME = 'parent_address';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE enrollments ADD COLUMN parent_address TEXT NULL COMMENT "Parent address" AFTER parent_contact_number', 
    'SELECT "✓ Column parent_address already exists" AS Status');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- 3. SAFELY ADD LRN TO LEARNERS TABLE
-- ============================================

-- Add lrn column if not exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'learners' 
AND COLUMN_NAME = 'lrn';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE learners ADD COLUMN lrn VARCHAR(12) UNIQUE NULL COMMENT "Learner Reference Number" AFTER id', 
    'SELECT "✓ Column lrn already exists" AS Status');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- 4. SAFELY ADD INDEXES
-- ============================================

-- Add index on learners.lrn if not exists
SET @index_exists = 0;
SELECT COUNT(*) INTO @index_exists 
FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'learners' 
AND INDEX_NAME = 'idx_lrn';

SET @query = IF(@index_exists = 0, 
    'CREATE INDEX idx_lrn ON learners(lrn)', 
    'SELECT "✓ Index idx_lrn already exists" AS Status');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index on enrollments.previous_lrn if not exists
SET @index_exists = 0;
SELECT COUNT(*) INTO @index_exists 
FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'enrollments' 
AND INDEX_NAME = 'idx_previous_lrn';

SET @query = IF(@index_exists = 0, 
    'CREATE INDEX idx_previous_lrn ON enrollments(previous_lrn)', 
    'SELECT "✓ Index idx_previous_lrn already exists" AS Status');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- 5. COMPLETION MESSAGE
-- ============================================

SELECT '✓ Safe Migration Complete!' as Status;
SELECT 'All columns and indexes have been checked and added if needed.' as Summary;
SELECT 'This script can be run multiple times safely.' as Note;

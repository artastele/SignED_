-- ============================================
-- SAFE DATABASE FIX
-- ============================================
-- This script safely checks and adds missing columns/tables
-- Run this in phpMyAdmin - it will skip if columns already exist

-- ============================================
-- 1. CHECK AND ADD COLUMNS TO IEPS TABLE
-- ============================================

-- Check if assessment_id exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'signed_system' 
AND TABLE_NAME = 'ieps' 
AND COLUMN_NAME = 'assessment_id';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE ieps ADD COLUMN assessment_id INT NULL AFTER learner_id', 
    'SELECT "Column assessment_id already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if draft_data exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'signed_system' 
AND TABLE_NAME = 'ieps' 
AND COLUMN_NAME = 'draft_data';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE ieps ADD COLUMN draft_data LONGTEXT NULL AFTER assessment_id', 
    'SELECT "Column draft_data already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if meeting_scheduled exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'signed_system' 
AND TABLE_NAME = 'ieps' 
AND COLUMN_NAME = 'meeting_scheduled';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE ieps ADD COLUMN meeting_scheduled TINYINT(1) DEFAULT 0 AFTER status', 
    'SELECT "Column meeting_scheduled already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- 2. CHECK AND ADD COLUMNS TO LEARNERS TABLE
-- ============================================

-- Check if school_year exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'signed_system' 
AND TABLE_NAME = 'learners' 
AND COLUMN_NAME = 'school_year';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE learners ADD COLUMN school_year VARCHAR(20) NULL AFTER grade_level', 
    'SELECT "Column school_year already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- 3. CHECK AND ADD COLUMNS TO ASSESSMENTS TABLE
-- ============================================

-- Check if previous_assessment_id exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'signed_system' 
AND TABLE_NAME = 'assessments' 
AND COLUMN_NAME = 'previous_assessment_id';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE assessments ADD COLUMN previous_assessment_id INT NULL AFTER learner_id', 
    'SELECT "Column previous_assessment_id already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- 4. CREATE IEP-RELATED TABLES (IF NOT EXISTS)
-- ============================================

-- IEP Goals table
CREATE TABLE IF NOT EXISTS iep_goals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NOT NULL,
    domain VARCHAR(100) NOT NULL COMMENT 'Developmental Domain',
    skill VARCHAR(255) NOT NULL COMMENT 'Skill name',
    description TEXT NOT NULL,
    quarter1_recommendation TEXT NULL,
    quarter2_recommendation TEXT NULL,
    mastered_yes TINYINT(1) DEFAULT 0,
    mastered_no TINYINT(1) DEFAULT 0,
    performance_level VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (iep_id) REFERENCES ieps(id) ON DELETE CASCADE,
    INDEX idx_iep_id (iep_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- IEP Services table
CREATE TABLE IF NOT EXISTS iep_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    provider VARCHAR(255) NULL,
    frequency VARCHAR(100) NULL,
    duration VARCHAR(100) NULL,
    location VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (iep_id) REFERENCES ieps(id) ON DELETE CASCADE,
    INDEX idx_iep_id (iep_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- IEP Accommodations table
CREATE TABLE IF NOT EXISTS iep_accommodations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NOT NULL,
    accommodation_type VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (iep_id) REFERENCES ieps(id) ON DELETE CASCADE,
    INDEX idx_iep_id (iep_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- IEP Meeting Attendees table
CREATE TABLE IF NOT EXISTS iep_meeting_attendees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    meeting_id INT NOT NULL,
    user_id INT NULL,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(100) NOT NULL,
    designation VARCHAR(255) NULL,
    attendance_status ENUM('invited', 'confirmed', 'attended', 'absent') DEFAULT 'invited',
    signature TEXT NULL,
    signed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (meeting_id) REFERENCES iep_meetings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_meeting_id (meeting_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Enrollment History table
CREATE TABLE IF NOT EXISTS enrollment_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    learner_id INT NOT NULL,
    school_year VARCHAR(20) NOT NULL,
    grade_level VARCHAR(50) NOT NULL,
    enrollment_id INT NULL,
    assessment_id INT NULL,
    status VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (learner_id) REFERENCES learners(id) ON DELETE CASCADE,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE SET NULL,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE SET NULL,
    INDEX idx_learner_year (learner_id, school_year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 5. VERIFICATION
-- ============================================
SELECT 'Database structure updated successfully!' AS status;

-- Show what was created
SELECT 'IEP Tables:' AS info;
SHOW TABLES LIKE 'iep_%';

SELECT 'Enrollment History:' AS info;
SHOW TABLES LIKE 'enrollment_history';

SELECT 'IEPs columns:' AS info;
SHOW COLUMNS FROM ieps;

SELECT 'Learners columns:' AS info;
SHOW COLUMNS FROM learners WHERE Field IN ('school_year', 'previous_learner_id');

SELECT 'Assessments columns:' AS info;
SHOW COLUMNS FROM assessments WHERE Field = 'previous_assessment_id';

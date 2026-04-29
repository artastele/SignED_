-- ============================================
-- COMPLETE DATABASE FIX
-- ============================================
-- Run these queries ONE BY ONE in phpMyAdmin
-- If you get "Duplicate column" error, just skip that query and continue

-- ============================================
-- 1. ADD COLUMNS TO IEPS TABLE
-- ============================================

-- Add assessment_id (skip if error)
ALTER TABLE ieps 
ADD COLUMN assessment_id INT NULL AFTER learner_id;

-- Add draft_data (skip if error)
ALTER TABLE ieps 
ADD COLUMN draft_data LONGTEXT NULL AFTER assessment_id;

-- Add meeting_scheduled (skip if error)
ALTER TABLE ieps 
ADD COLUMN meeting_scheduled TINYINT(1) DEFAULT 0 AFTER status;

-- ============================================
-- 2. ADD COLUMNS TO LEARNERS TABLE
-- ============================================

-- Add school_year (skip if error)
ALTER TABLE learners 
ADD COLUMN school_year VARCHAR(20) NULL AFTER grade_level;

-- ============================================
-- 3. ADD COLUMNS TO ASSESSMENTS TABLE
-- ============================================

-- Add previous_assessment_id (skip if error)
ALTER TABLE assessments 
ADD COLUMN previous_assessment_id INT NULL AFTER learner_id;

-- ============================================
-- 4. FIX IEP_MEETINGS TABLE
-- ============================================

-- Add iep_id column (skip if error)
ALTER TABLE iep_meetings 
ADD COLUMN iep_id INT NULL AFTER id;

-- Add index for iep_id (skip if error)
ALTER TABLE iep_meetings 
ADD INDEX idx_iep_id (iep_id);

-- Change meeting_date from DATETIME to DATE (skip if error)
ALTER TABLE iep_meetings 
MODIFY COLUMN meeting_date DATE NOT NULL;

-- Add meeting_time column (skip if error)
ALTER TABLE iep_meetings 
ADD COLUMN meeting_time TIME NOT NULL AFTER meeting_date;

-- ============================================
-- 5. CREATE IEP-RELATED TABLES
-- ============================================
-- These use "IF NOT EXISTS" so they won't error if tables exist

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
-- 6. VERIFICATION QUERIES
-- ============================================
-- Run these to check what was created

SELECT 'Checking IEPs table columns...' AS status;
SHOW COLUMNS FROM ieps;

SELECT 'Checking Learners table columns...' AS status;
SHOW COLUMNS FROM learners;

SELECT 'Checking Assessments table columns...' AS status;
SHOW COLUMNS FROM assessments;

SELECT 'Checking IEP Meetings table columns...' AS status;
SHOW COLUMNS FROM iep_meetings;

SELECT 'Checking IEP-related tables...' AS status;
SHOW TABLES LIKE 'iep_%';

SELECT 'Checking Enrollment History table...' AS status;
SHOW TABLES LIKE 'enrollment_history';

SELECT 'Database fix completed!' AS status;

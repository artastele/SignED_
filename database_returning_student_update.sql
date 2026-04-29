-- ============================================
-- DATABASE UPDATE: Returning Student Feature
-- ============================================
-- This update adds support for tracking returning students
-- and linking their new records to previous year's data

-- NOTE: previous_learner_id already exists in learners table
-- Run these one by one and ignore errors if column already exists

-- Add school_year to learners table
-- Run this: If error "Duplicate column", ignore it
ALTER TABLE learners 
ADD COLUMN school_year VARCHAR(20) NULL AFTER grade_level;

-- Add previous_assessment_id to assessments table
-- Run this: If error "Duplicate column", ignore it
ALTER TABLE assessments 
ADD COLUMN previous_assessment_id INT NULL AFTER learner_id;

-- Add index for previous_assessment_id
-- Run this: If error "Duplicate key", ignore it
ALTER TABLE assessments 
ADD INDEX idx_previous_assessment (previous_assessment_id);

-- Add enrollment_history table to track year-to-year progression
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
-- VERIFICATION QUERIES:
-- ============================================
-- Check if columns were added:
-- SHOW COLUMNS FROM learners LIKE '%school_year%';
-- SHOW COLUMNS FROM assessments LIKE '%previous%';
-- DESCRIBE enrollment_history;

-- ============================================
-- NOTES:
-- ============================================
-- previous_learner_id: Already exists - Links to the learner's record from previous year
-- previous_assessment_id: Links to the assessment from previous year
-- school_year: Tracks which school year this record belongs to (e.g. "2025-2026")
-- enrollment_history: Complete history of learner's enrollments across years

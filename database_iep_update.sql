-- ============================================
-- DATABASE UPDATE: IEP Creation & Meeting
-- ============================================

-- Add missing columns to ieps table
ALTER TABLE ieps 
ADD COLUMN assessment_id INT NULL AFTER learner_id;

ALTER TABLE ieps 
ADD COLUMN draft_data LONGTEXT NULL AFTER assessment_id;

ALTER TABLE ieps 
ADD COLUMN meeting_scheduled TINYINT(1) DEFAULT 0 AFTER status;

-- Add foreign key for assessment_id
ALTER TABLE ieps 
ADD FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE SET NULL;

-- IEP Goals table
CREATE TABLE IF NOT EXISTS iep_goals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NOT NULL,
    domain VARCHAR(100) NOT NULL COMMENT 'Developmental Domain (e.g. Perceptuo-Cognitive)',
    skill VARCHAR(255) NOT NULL COMMENT 'Skill name (e.g. Cognitive, Fine Motor)',
    description TEXT NOT NULL,
    quarter1_recommendation TEXT NULL COMMENT 'Educational Recommendation Quarter 1',
    quarter2_recommendation TEXT NULL COMMENT 'Educational Recommendation Quarter 2',
    mastered_yes TINYINT(1) DEFAULT 0,
    mastered_no TINYINT(1) DEFAULT 0,
    performance_level VARCHAR(50) NULL COMMENT 'Beginning/Developing/Approaching/Proficient/Advanced',
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
    role VARCHAR(100) NOT NULL COMMENT 'SPED Teacher, Gen.Ed Teacher, Parent, Medical Allied Health, etc.',
    designation VARCHAR(255) NULL,
    attendance_status ENUM('invited', 'confirmed', 'attended', 'absent') DEFAULT 'invited',
    signature TEXT NULL,
    signed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (meeting_id) REFERENCES iep_meetings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_meeting_id (meeting_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- VERIFICATION QUERIES:
-- ============================================
-- DESCRIBE ieps;
-- DESCRIBE iep_goals;
-- DESCRIBE iep_services;
-- DESCRIBE iep_accommodations;
-- DESCRIBE iep_meeting_attendees;

-- ============================================
-- NOTES:
-- ============================================
-- assessment_id: Links IEP to the assessment that was submitted
-- draft_data: Stores complete IEP form data as JSON for drafts
-- meeting_scheduled: Flag to indicate if meeting has been scheduled
-- iep_goals: Based on IEP P2.pdf table structure
-- iep_meeting_attendees: Multi-Disciplinary Team signatures

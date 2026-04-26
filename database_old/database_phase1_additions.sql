-- ============================================
-- PHASE 1: FOUNDATION - DATABASE ADDITIONS
-- SignED SPED System
-- ============================================

USE signed_system;

-- ============================================
-- 1. UPDATE USERS TABLE FOR SPED ROLES
-- ============================================

-- Update role enum to include all SPED roles
ALTER TABLE users 
MODIFY COLUMN role ENUM('admin', 'teacher', 'parent', 'sped_teacher', 'guidance', 'principal', 'learner') NOT NULL;

-- Add additional user fields for SPED system
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS phone VARCHAR(20) NULL AFTER email,
ADD COLUMN IF NOT EXISTS address TEXT NULL AFTER phone,
ADD COLUMN IF NOT EXISTS emergency_contact VARCHAR(100) NULL AFTER address,
ADD COLUMN IF NOT EXISTS emergency_phone VARCHAR(20) NULL AFTER emergency_contact,
ADD COLUMN IF NOT EXISTS is_temp_password BOOLEAN DEFAULT FALSE AFTER otp_expires_at,
ADD COLUMN IF NOT EXISTS must_change_password BOOLEAN DEFAULT FALSE AFTER is_temp_password,
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL AFTER must_change_password;

-- ============================================
-- 2. ANNOUNCEMENTS TABLE (if not exists)
-- ============================================

CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    posted_by INT NOT NULL,
    target_role ENUM('all', 'parent', 'learner', 'sped_teacher', 'guidance', 'principal', 'admin') DEFAULT 'all',
    is_active BOOLEAN DEFAULT TRUE,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_target_role (target_role),
    INDEX idx_active_expires (is_active, expires_at)
);

-- Add missing columns to existing announcements table if they don't exist
-- Note: MySQL doesn't support IF NOT EXISTS for ALTER TABLE ADD COLUMN in older versions
-- So we'll use a procedure to check first

DELIMITER $$

CREATE PROCEDURE AddAnnouncementColumns()
BEGIN
    -- Check and add is_active column
    IF NOT EXISTS (
        SELECT * FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'announcements' 
        AND COLUMN_NAME = 'is_active'
    ) THEN
        ALTER TABLE announcements ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER target_role;
    END IF;
    
    -- Check and add priority column
    IF NOT EXISTS (
        SELECT * FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'announcements' 
        AND COLUMN_NAME = 'priority'
    ) THEN
        ALTER TABLE announcements ADD COLUMN priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal' AFTER is_active;
    END IF;
    
    -- Check and add updated_at column
    IF NOT EXISTS (
        SELECT * FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'announcements' 
        AND COLUMN_NAME = 'updated_at'
    ) THEN
        ALTER TABLE announcements ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;
    END IF;
END$$

DELIMITER ;

-- Execute the procedure
CALL AddAnnouncementColumns();

-- Drop the procedure after use
DROP PROCEDURE IF EXISTS AddAnnouncementColumns;

-- ============================================
-- 3. NOTIFICATIONS TABLE (if not exists)
-- ============================================

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    category ENUM('enrollment', 'assessment', 'iep', 'meeting', 'system', 'general') DEFAULT 'general',
    is_read BOOLEAN DEFAULT FALSE,
    link VARCHAR(255) NULL,
    related_entity_type ENUM('enrollment', 'assessment', 'iep', 'meeting', 'learner') NULL,
    related_entity_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read),
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_category (category)
);

-- ============================================
-- 4. LEARNERS TABLE ENHANCEMENTS
-- ============================================

-- Add LRN and additional fields to learners table
ALTER TABLE learners 
ADD COLUMN IF NOT EXISTS lrn VARCHAR(20) UNIQUE NULL AFTER id,
ADD COLUMN IF NOT EXISTS middle_name VARCHAR(50) NULL AFTER first_name,
ADD COLUMN IF NOT EXISTS suffix VARCHAR(10) NULL AFTER last_name,
ADD COLUMN IF NOT EXISTS gender ENUM('male', 'female') NULL AFTER date_of_birth,
ADD COLUMN IF NOT EXISTS place_of_birth VARCHAR(255) NULL AFTER gender,
ADD COLUMN IF NOT EXISTS nationality VARCHAR(50) DEFAULT 'Filipino' AFTER place_of_birth,
ADD COLUMN IF NOT EXISTS religion VARCHAR(50) NULL AFTER nationality,
ADD COLUMN IF NOT EXISTS mother_tongue VARCHAR(50) NULL AFTER religion,
ADD COLUMN IF NOT EXISTS indigenous_people VARCHAR(100) NULL AFTER mother_tongue,
ADD COLUMN IF NOT EXISTS is_4ps_beneficiary BOOLEAN DEFAULT FALSE AFTER indigenous_people,
ADD COLUMN IF NOT EXISTS learner_reference_number VARCHAR(20) NULL AFTER is_4ps_beneficiary;

-- ============================================
-- 5. ENROLLMENTS TABLE ENHANCEMENTS
-- ============================================

-- Add BEEF form data and education history
ALTER TABLE enrollments 
ADD COLUMN IF NOT EXISTS beef_data JSON NULL COMMENT 'Complete BEEF form data' AFTER learner_grade,
ADD COLUMN IF NOT EXISTS education_history JSON NULL COMMENT 'Learner education history' AFTER beef_data,
ADD COLUMN IF NOT EXISTS parent_contact_number VARCHAR(20) NULL AFTER parent_id,
ADD COLUMN IF NOT EXISTS parent_address TEXT NULL AFTER parent_contact_number,
ADD COLUMN IF NOT EXISTS is_returning_student BOOLEAN DEFAULT FALSE AFTER education_history,
ADD COLUMN IF NOT EXISTS previous_lrn VARCHAR(20) NULL AFTER is_returning_student;

-- ============================================
-- 6. ASSESSMENTS TABLE ENHANCEMENTS
-- ============================================

-- Add Part B data and additional assessment fields
ALTER TABLE assessments 
ADD COLUMN IF NOT EXISTS part_b_data JSON NULL COMMENT 'Optional Part B assessment data' AFTER recommendations,
ADD COLUMN IF NOT EXISTS iep_p1_data JSON NULL COMMENT 'IEP Part 1 form data' AFTER part_b_data,
ADD COLUMN IF NOT EXISTS assessment_status ENUM('draft', 'in_progress', 'completed', 'reviewed') DEFAULT 'draft' AFTER assessment_date,
ADD COLUMN IF NOT EXISTS completed_by_parent BOOLEAN DEFAULT FALSE AFTER assessment_status,
ADD COLUMN IF NOT EXISTS parent_completed_at TIMESTAMP NULL AFTER completed_by_parent;

-- ============================================
-- 7. SYSTEM SETTINGS TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    updated_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_key (setting_key),
    INDEX idx_public (is_public)
);

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('school_name', 'SignED Special Education Program', 'string', 'School name displayed in the system', TRUE),
('school_logo_path', '/assets/images/signed-logo.png', 'string', 'Path to school logo', TRUE),
('school_address', '', 'string', 'School physical address', TRUE),
('school_contact', '', 'string', 'School contact number', TRUE),
('school_email', '', 'string', 'School email address', TRUE),
('enrollment_open', 'true', 'boolean', 'Whether enrollment is currently open', TRUE),
('max_file_size_mb', '5', 'number', 'Maximum file upload size in MB', FALSE),
('session_timeout_minutes', '15', 'number', 'Session timeout in minutes', FALSE),
('enable_notifications', 'true', 'boolean', 'Enable system notifications', FALSE),
('enable_email_notifications', 'true', 'boolean', 'Enable email notifications', FALSE)
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ============================================
-- 8. ACTIVITY LOG TABLE (for user actions)
-- ============================================

CREATE TABLE IF NOT EXISTS activity_log (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NULL,
    entity_id INT NULL,
    description TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created (created_at)
);

-- ============================================
-- 9. FILE UPLOADS TRACKING TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS file_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uploaded_by INT NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_type ENUM('document', 'image', 'pdf', 'other') DEFAULT 'document',
    related_entity_type ENUM('enrollment', 'assessment', 'iep', 'learner', 'announcement') NULL,
    related_entity_id INT NULL,
    is_encrypted BOOLEAN DEFAULT FALSE,
    encryption_key_id VARCHAR(100) NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_uploaded_by (uploaded_by),
    INDEX idx_entity (related_entity_type, related_entity_id),
    INDEX idx_uploaded_at (uploaded_at)
);

-- ============================================
-- 10. CREATE DEFAULT ADMIN USER (if not exists)
-- ============================================

INSERT INTO users (fullname, email, password, role, is_verified, auth_provider) 
SELECT 'System Administrator', 'admin@signed.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 'local'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@signed.local');
-- Default password: password (change this immediately in production!)

-- ============================================
-- 11. CREATE SAMPLE ANNOUNCEMENT
-- ============================================

-- Insert sample announcement only if admin user exists and announcement doesn't exist
INSERT INTO announcements (title, content, posted_by, target_role, priority)
SELECT 
    'Welcome to SignED SPED System',
    'Welcome to the Special Education Management System. This system helps manage the enrollment, assessment, and IEP processes for SPED learners. Please complete your enrollment requirements to proceed.',
    u.id,
    'parent',
    'high'
FROM users u
WHERE u.role = 'admin' 
  AND NOT EXISTS (SELECT 1 FROM announcements WHERE title = 'Welcome to SignED SPED System')
LIMIT 1;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Verify tables were created
SELECT 'Tables created successfully' AS status;

-- Show all tables
SHOW TABLES;

-- Verify users table structure
DESCRIBE users;

-- Verify announcements table
DESCRIBE announcements;

-- Verify notifications table
DESCRIBE notifications;

-- Count existing records
SELECT 
    (SELECT COUNT(*) FROM users) AS total_users,
    (SELECT COUNT(*) FROM announcements) AS total_announcements,
    (SELECT COUNT(*) FROM notifications) AS total_notifications;

-- ============================================
-- SignED SPED System - Master Database Setup
-- Complete database setup for fresh installation
-- Run this file ONCE on a new PC/server
-- ============================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS signed_system;
USE signed_system;

-- ============================================
-- TABLE 1: Users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NOT NULL,
    suffix VARCHAR(20) NULL,
    fullname VARCHAR(255) GENERATED ALWAYS AS (
        CONCAT(
            first_name, ' ',
            IFNULL(CONCAT(middle_name, ' '), ''),
            last_name,
            IFNULL(CONCAT(' ', suffix), '')
        )
    ) STORED,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NULL,
    role ENUM('admin', 'teacher', 'parent', 'sped_teacher', 'guidance', 'principal', 'learner') NULL DEFAULT NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    auth_provider ENUM('local', 'google') DEFAULT 'local',
    google_id VARCHAR(255) NULL UNIQUE,
    is_verified BOOLEAN DEFAULT FALSE,
    otp_code VARCHAR(6) NULL,
    otp_expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_google_id (google_id)
);

-- Insert default admin account
INSERT INTO users (first_name, middle_name, last_name, email, password, role, is_verified, auth_provider) 
VALUES ('System', NULL, 'Administrator', 'admin@signed.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 'local')
ON DUPLICATE KEY UPDATE email = email;

-- ============================================
-- TABLE 2: Enrollments
-- ============================================
CREATE TABLE IF NOT EXISTS enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    parent_id INT NOT NULL,
    learner_first_name VARCHAR(100) NOT NULL,
    learner_middle_name VARCHAR(100) NULL,
    learner_last_name VARCHAR(100) NOT NULL,
    learner_suffix VARCHAR(20) NULL,
    learner_birthdate DATE NOT NULL,
    learner_age INT NOT NULL,
    learner_sex ENUM('Male', 'Female') NOT NULL,
    learner_address TEXT NOT NULL,
    learner_disability_type VARCHAR(255) NOT NULL,
    learner_disability_cause TEXT NULL,
    parent_name VARCHAR(255) NOT NULL,
    parent_contact VARCHAR(20) NOT NULL,
    parent_email VARCHAR(255) NOT NULL,
    parent_address TEXT NOT NULL,
    parent_relationship VARCHAR(50) NOT NULL,
    birth_certificate_path VARCHAR(500) NULL,
    status ENUM('pending_verification', 'verified', 'approved', 'rejected') DEFAULT 'pending_verification',
    rejection_reason TEXT NULL,
    verified_by INT NULL,
    verified_at TIMESTAMP NULL,
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_parent_id (parent_id)
);

-- ============================================
-- TABLE 3: Learners
-- ============================================
CREATE TABLE IF NOT EXISTS learners (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    enrollment_id INT NOT NULL,
    lrn VARCHAR(12) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NOT NULL,
    suffix VARCHAR(20) NULL,
    birthdate DATE NOT NULL,
    age INT NOT NULL,
    sex ENUM('Male', 'Female') NOT NULL,
    address TEXT NOT NULL,
    disability_type VARCHAR(255) NOT NULL,
    disability_cause TEXT NULL,
    parent_id INT NOT NULL,
    status ENUM('active', 'inactive', 'graduated', 'transferred') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_lrn (lrn),
    INDEX idx_status (status)
);

-- ============================================
-- TABLE 4: LRN Generation Log
-- ============================================
CREATE TABLE IF NOT EXISTS lrn_generation_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    enrollment_id INT NOT NULL,
    learner_id INT NOT NULL,
    lrn VARCHAR(12) NOT NULL,
    generated_by INT NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
    FOREIGN KEY (learner_id) REFERENCES learners(id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- TABLE 5: Assessments
-- ============================================
CREATE TABLE IF NOT EXISTS assessments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    learner_id INT NOT NULL,
    enrollment_id INT NOT NULL,
    parent_id INT NOT NULL,
    
    -- Section 1: Learner Information (Auto-filled from BEEF)
    learner_name VARCHAR(255) NOT NULL,
    learner_sex ENUM('Male', 'Female') NOT NULL,
    learner_birthdate DATE NOT NULL,
    learner_age INT NOT NULL,
    learner_address TEXT NOT NULL,
    learner_disability_type VARCHAR(255) NOT NULL,
    learner_disability_cause TEXT NULL,
    parent_name VARCHAR(255) NOT NULL,
    parent_contact VARCHAR(20) NOT NULL,
    parent_email VARCHAR(255) NOT NULL,
    
    -- Section 2: Educational Background (Parent fills)
    previous_school VARCHAR(255) NULL,
    previous_grade_level VARCHAR(50) NULL,
    previous_school_year VARCHAR(20) NULL,
    academic_performance TEXT NULL,
    special_education_services TEXT NULL,
    learning_difficulties TEXT NULL,
    
    -- Section 3: Additional Information (Optional)
    medical_conditions TEXT NULL,
    medications TEXT NULL,
    allergies TEXT NULL,
    behavioral_concerns TEXT NULL,
    communication_methods TEXT NULL,
    assistive_devices TEXT NULL,
    additional_notes TEXT NULL,
    
    -- Status tracking
    status ENUM('draft', 'submitted', 'reviewed') DEFAULT 'draft',
    submitted_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (learner_id) REFERENCES learners(id) ON DELETE CASCADE,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_learner_id (learner_id)
);

-- ============================================
-- TABLE 6: IEPs (Individualized Education Plans)
-- ============================================
CREATE TABLE IF NOT EXISTS ieps (
    id INT PRIMARY KEY AUTO_INCREMENT,
    learner_id INT NOT NULL,
    assessment_id INT NULL,
    created_by INT NOT NULL,
    
    -- IEP Details
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    goals TEXT NOT NULL,
    accommodations TEXT NOT NULL,
    modifications TEXT NOT NULL,
    services TEXT NOT NULL,
    
    -- Status tracking
    status ENUM('draft', 'pending_approval', 'approved', 'rejected', 'active', 'completed') DEFAULT 'draft',
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (learner_id) REFERENCES learners(id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_learner_id (learner_id)
);

-- ============================================
-- TABLE 7: IEP Meetings
-- ============================================
CREATE TABLE IF NOT EXISTS iep_meetings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NULL,
    learner_id INT NOT NULL,
    scheduled_by INT NOT NULL,
    meeting_date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    agenda TEXT NULL,
    notes TEXT NULL,
    status ENUM('scheduled', 'confirmed', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (iep_id) REFERENCES ieps(id) ON DELETE SET NULL,
    FOREIGN KEY (learner_id) REFERENCES learners(id) ON DELETE CASCADE,
    FOREIGN KEY (scheduled_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_meeting_date (meeting_date)
);

-- ============================================
-- TABLE 8: Learning Materials
-- ============================================
CREATE TABLE IF NOT EXISTS learning_materials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    learner_id INT NOT NULL,
    iep_id INT NULL,
    uploaded_by INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    status ENUM('active', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (learner_id) REFERENCES learners(id) ON DELETE CASCADE,
    FOREIGN KEY (iep_id) REFERENCES ieps(id) ON DELETE SET NULL,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_learner_id (learner_id),
    INDEX idx_status (status)
);

-- ============================================
-- TABLE 9: Document Store
-- ============================================
CREATE TABLE IF NOT EXISTS document_store (
    id INT PRIMARY KEY AUTO_INCREMENT,
    entity_type ENUM('enrollment', 'assessment', 'iep', 'meeting', 'learner') NOT NULL,
    entity_id INT NOT NULL,
    document_type VARCHAR(100) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_entity (entity_type, entity_id)
);

-- ============================================
-- TABLE 10: System Settings
-- ============================================
CREATE TABLE IF NOT EXISTS system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_setting_key (setting_key)
);

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('system_name', 'SignED SPED System', 'string', 'System name displayed in header'),
('session_timeout', '30', 'number', 'Session timeout in minutes'),
('max_login_attempts', '5', 'number', 'Maximum login attempts before lockout'),
('require_email_verification', '1', 'boolean', 'Require email verification for new users'),
('enable_audit_logging', '1', 'boolean', 'Enable audit logging for all actions'),
('smtp_host', 'smtp.gmail.com', 'string', 'SMTP server host'),
('smtp_port', '587', 'number', 'SMTP server port'),
('smtp_from_email', 'noreply@signed.edu', 'string', 'From email address for system emails')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ============================================
-- TABLE 11: Announcements
-- ============================================
CREATE TABLE IF NOT EXISTS announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    target_audience ENUM('all', 'parents', 'teachers', 'sped_staff', 'learners', 'admins') DEFAULT 'all',
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_active (is_active),
    INDEX idx_priority (priority),
    INDEX idx_target (target_audience),
    INDEX idx_created (created_at)
);

-- ============================================
-- TABLE 12: Announcement Reads
-- ============================================
CREATE TABLE IF NOT EXISTS announcement_reads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    announcement_id INT NOT NULL,
    user_id INT NOT NULL,
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_read (announcement_id, user_id),
    INDEX idx_user (user_id)
);

-- ============================================
-- TABLE 13: Notifications
-- ============================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error', 'announcement') DEFAULT 'info',
    link VARCHAR(500) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
);

-- ============================================
-- TABLE 14: Audit Logs
-- ============================================
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    action_type VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NULL,
    entity_id INT NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    additional_data JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action_type (action_type),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created_at (created_at)
);

-- ============================================
-- TABLE 15: Error Logs
-- ============================================
CREATE TABLE IF NOT EXISTS error_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    error_type VARCHAR(100) NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    error_message TEXT NOT NULL,
    stack_trace TEXT NULL,
    request_data JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_severity (severity),
    INDEX idx_error_type (error_type),
    INDEX idx_created_at (created_at)
);

-- ============================================
-- VERIFICATION
-- ============================================
SELECT '========================================' as separator;
SELECT '✅ DATABASE SETUP COMPLETE!' as status;
SELECT '========================================' as separator;

-- Show created tables
SELECT 'Tables created:' as info;
SHOW TABLES;

-- Show default admin account
SELECT 'Default admin account:' as info;
SELECT id, email, role, is_verified FROM users WHERE role = 'admin';

-- Show system settings
SELECT 'System settings:' as info;
SELECT setting_key, setting_value FROM system_settings;

SELECT '========================================' as separator;
SELECT '📋 NEXT STEPS:' as info;
SELECT '1. Login as admin: admin@signed.local / password' as step1;
SELECT '2. Change admin password immediately' as step2;
SELECT '3. Configure SMTP settings in Admin > Settings' as step3;
SELECT '4. System is ready to use!' as step4;
SELECT '========================================' as separator;

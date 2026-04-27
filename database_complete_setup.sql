-- ============================================
-- SignED SPED System - Complete Database Setup
-- ============================================
-- This file contains the complete database schema for the SignED SPED System
-- Run this file in phpMyAdmin to set up the entire database from scratch
-- 
-- Instructions:
-- 1. Open phpMyAdmin
-- 2. Click on "Import" tab
-- 3. Choose this file
-- 4. Click "Go"
-- 
-- Default Admin Credentials:
-- Email: admin@signed.local
-- Password: password (CHANGE THIS IMMEDIATELY!)
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS signed_system;
USE signed_system;

-- ============================================
-- 1. USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50) NULL,
    last_name VARCHAR(50) NOT NULL,
    suffix VARCHAR(10) NULL COMMENT 'Jr., Sr., III, etc.',
    fullname VARCHAR(100) GENERATED ALWAYS AS (
        CONCAT_WS(' ', 
            first_name, 
            middle_name, 
            last_name,
            CASE WHEN suffix IS NOT NULL THEN suffix ELSE NULL END
        )
    ) STORED,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NULL,
    google_id VARCHAR(255) NULL,
    auth_provider ENUM('local', 'google') NOT NULL DEFAULT 'local',
    role ENUM('admin', 'teacher', 'parent', 'learner', 'sped_teacher', 'guidance', 'principal') NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    emergency_contact VARCHAR(100) NULL,
    emergency_phone VARCHAR(20) NULL,
    is_verified TINYINT(1) DEFAULT 0,
    otp_code VARCHAR(6) NULL,
    otp_expires_at TIMESTAMP NULL,
    is_temp_password BOOLEAN DEFAULT FALSE,
    must_change_password BOOLEAN DEFAULT FALSE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_email (email),
    INDEX idx_auth_provider (auth_provider),
    INDEX idx_first_name (first_name),
    INDEX idx_last_name (last_name)
);

-- ============================================
-- 2. LEARNERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS learners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lrn VARCHAR(20) UNIQUE NULL,
    user_id INT NOT NULL,
    parent_id INT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50) NULL,
    last_name VARCHAR(50) NOT NULL,
    suffix VARCHAR(10) NULL,
    date_of_birth DATE NULL,
    gender ENUM('male', 'female') NULL,
    place_of_birth VARCHAR(255) NULL,
    nationality VARCHAR(50) DEFAULT 'Filipino',
    religion VARCHAR(50) NULL,
    mother_tongue VARCHAR(50) NULL,
    indigenous_people VARCHAR(100) NULL,
    is_4ps_beneficiary BOOLEAN DEFAULT FALSE,
    learner_reference_number VARCHAR(20) NULL,
    grade_level VARCHAR(10) NULL,
    disability_type VARCHAR(100) NULL,
    status ENUM('pending_info', 'enrolled', 'assessment_pending', 'assessment_complete', 
                'iep_meeting_scheduled', 'iep_meeting_complete', 
                'iep_pending_approval', 'iep_approved', 'active') NOT NULL DEFAULT 'enrolled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_parent (parent_id),
    INDEX idx_user (user_id),
    INDEX idx_lrn (lrn)
);

-- ============================================
-- 3. ENROLLMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    learner_first_name VARCHAR(50) NOT NULL,
    learner_last_name VARCHAR(50) NOT NULL,
    learner_dob DATE NOT NULL,
    learner_grade VARCHAR(10) NOT NULL,
    beef_data JSON NULL COMMENT 'Complete BEEF form data',
    education_history JSON NULL COMMENT 'Learner education history',
    is_returning_student TINYINT(1) DEFAULT 0,
    previous_lrn VARCHAR(20) NULL,
    parent_contact_number VARCHAR(20) NULL,
    parent_address TEXT NULL,
    status ENUM('pending_documents', 'pending_verification', 'approved', 'rejected') NOT NULL DEFAULT 'pending_documents',
    rejection_reason TEXT NULL,
    verified_by INT NULL,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_parent (parent_id),
    INDEX idx_verified_by (verified_by),
    INDEX idx_previous_lrn (previous_lrn)
);

-- ============================================
-- 4. ENROLLMENT DOCUMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS enrollment_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_id INT NOT NULL,
    document_type ENUM('psa', 'pwd_id', 'medical_record', 'beef') NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    encrypted_filename VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    encryption_key_id VARCHAR(100) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment_document (enrollment_id, document_type),
    INDEX idx_enrollment (enrollment_id),
    INDEX idx_document_type (document_type)
);

-- ============================================
-- 5. ASSESSMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    learner_id INT NOT NULL,
    assessed_by INT NOT NULL,
    cognitive_ability TEXT NOT NULL,
    communication_skills TEXT NOT NULL,
    social_emotional_development TEXT NOT NULL,
    adaptive_behavior TEXT NOT NULL,
    academic_performance TEXT NOT NULL,
    recommendations TEXT NULL,
    part_b_data JSON NULL COMMENT 'Optional Part B assessment data',
    iep_p1_data JSON NULL COMMENT 'IEP Part 1 form data',
    assessment_date DATE NOT NULL,
    assessment_status ENUM('draft', 'in_progress', 'completed', 'reviewed') DEFAULT 'draft',
    completed_by_parent BOOLEAN DEFAULT FALSE,
    parent_completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (learner_id) REFERENCES learners(id) ON DELETE CASCADE,
    FOREIGN KEY (assessed_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_learner (learner_id),
    INDEX idx_assessor (assessed_by),
    INDEX idx_assessment_date (assessment_date)
);

-- ============================================
-- 6. IEP MEETINGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS iep_meetings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    learner_id INT NOT NULL,
    scheduled_by INT NOT NULL,
    meeting_date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    status ENUM('scheduled', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
    meeting_notes TEXT NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (learner_id) REFERENCES learners(id) ON DELETE CASCADE,
    FOREIGN KEY (scheduled_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_learner (learner_id),
    INDEX idx_status (status),
    INDEX idx_meeting_date (meeting_date),
    INDEX idx_scheduled_by (scheduled_by)
);

-- ============================================
-- 7. IEP MEETING PARTICIPANTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS iep_meeting_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meeting_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('sped_teacher', 'parent', 'guidance', 'principal', 'other') NOT NULL,
    attendance_status ENUM('invited', 'confirmed', 'declined', 'attended') NOT NULL DEFAULT 'invited',
    signature_data TEXT NULL,
    signed_at TIMESTAMP NULL,
    FOREIGN KEY (meeting_id) REFERENCES iep_meetings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_meeting_participant (meeting_id, user_id),
    INDEX idx_meeting (meeting_id),
    INDEX idx_user (user_id),
    INDEX idx_attendance_status (attendance_status)
);

-- ============================================
-- 8. IEPS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS ieps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    learner_id INT NOT NULL,
    created_by INT NOT NULL,
    meeting_id INT NULL,
    present_level_performance TEXT NOT NULL,
    annual_goals TEXT NOT NULL,
    short_term_objectives TEXT NOT NULL,
    special_education_services TEXT NOT NULL,
    accommodations TEXT NOT NULL,
    progress_measurement TEXT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('draft', 'pending_approval', 'approved', 'rejected', 'active', 'expired') NOT NULL DEFAULT 'draft',
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    digital_signature TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (learner_id) REFERENCES learners(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (meeting_id) REFERENCES iep_meetings(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_learner (learner_id),
    INDEX idx_status (status),
    INDEX idx_creator (created_by),
    INDEX idx_approved_by (approved_by)
);

-- ============================================
-- 9. LEARNING MATERIALS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS learning_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    iep_id INT NOT NULL,
    uploaded_by INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    iep_objective TEXT NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    encrypted_filename VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    encryption_key_id VARCHAR(100) NOT NULL,
    due_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (iep_id) REFERENCES ieps(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_iep (iep_id),
    INDEX idx_uploader (uploaded_by),
    INDEX idx_due_date (due_date)
);

-- ============================================
-- 10. LEARNER SUBMISSIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS learner_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT NOT NULL,
    learner_id INT NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    encrypted_filename VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    encryption_key_id VARCHAR(100) NOT NULL,
    submission_notes TEXT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_by INT NULL,
    review_notes TEXT NULL,
    reviewed_at TIMESTAMP NULL,
    FOREIGN KEY (material_id) REFERENCES learning_materials(id) ON DELETE CASCADE,
    FOREIGN KEY (learner_id) REFERENCES learners(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_material (material_id),
    INDEX idx_learner (learner_id),
    INDEX idx_submitted (submitted_at),
    INDEX idx_reviewed_by (reviewed_by)
);

-- ============================================
-- 11. DOCUMENT STORE TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS document_store (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    document_type ENUM('enrollment', 'assessment', 'iep', 'learning_material', 'submission', 'meeting') NOT NULL,
    classification ENUM('public', 'internal', 'confidential', 'restricted') NOT NULL DEFAULT 'internal',
    original_filename VARCHAR(255) NOT NULL,
    encrypted_filename VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    encryption_key_id VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_document_type (document_type),
    INDEX idx_classification (classification),
    INDEX idx_created_at (created_at)
);

-- ============================================
-- 12. AUDIT LOGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action_type ENUM('login', 'logout', 'document_access', 'document_upload', 
                     'status_change', 'role_change', 'approval', 'rejection', 
                     'meeting_schedule', 'email_sent', 'error') NOT NULL,
    entity_type ENUM('user', 'learner', 'enrollment', 'assessment', 'iep_meeting', 
                     'iep', 'learning_material', 'submission') NULL,
    entity_id INT NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    additional_data JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action_type (action_type),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created_at (created_at)
);

-- ============================================
-- 13. ERROR LOGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS error_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    error_type ENUM('file_upload', 'database', 'email', 'encryption', 'validation', 'system') NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') NOT NULL,
    error_message TEXT NOT NULL,
    stack_trace TEXT NULL,
    request_data JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_error_type (error_type),
    INDEX idx_severity (severity),
    INDEX idx_created_at (created_at)
);

-- ============================================
-- 14. ANNOUNCEMENTS TABLE
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

-- ============================================
-- 15. NOTIFICATIONS TABLE
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
-- 16. SYSTEM SETTINGS TABLE
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

-- ============================================
-- 17. ACTIVITY LOG TABLE
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
-- 18. FILE UPLOADS TABLE
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
-- INSERT DEFAULT DATA
-- ============================================

-- Insert default admin user
INSERT INTO users (first_name, last_name, email, password, role, is_verified, auth_provider) 
VALUES ('System', 'Administrator', 'admin@signed.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 'local')
ON DUPLICATE KEY UPDATE first_name = first_name;
-- Default password: password (CHANGE THIS IMMEDIATELY!)

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('school_name', 'SignED Special Education Program', 'string', 'School name displayed in the system', TRUE),
('school_logo_path', '/assets/images/SIGNED LOGO.png', 'string', 'Path to school logo', TRUE),
('school_address', '', 'string', 'School physical address', TRUE),
('school_contact', '', 'string', 'School contact number', TRUE),
('school_email', '', 'string', 'School email address', TRUE),
('enrollment_open', 'true', 'boolean', 'Whether enrollment is currently open', TRUE),
('max_file_size_mb', '10', 'number', 'Maximum file upload size in MB', FALSE),
('session_timeout_minutes', '30', 'number', 'Session timeout in minutes', FALSE),
('enable_notifications', 'true', 'boolean', 'Enable system notifications', FALSE),
('enable_email_notifications', 'true', 'boolean', 'Enable email notifications', FALSE)
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Insert welcome announcement
INSERT INTO announcements (title, content, posted_by, target_role, priority)
SELECT 
    'Welcome to SignED SPED System',
    'Welcome to the Special Education Management System. This system helps manage the enrollment, assessment, and IEP processes for SPED learners. Please complete your enrollment requirements to proceed.',
    u.id,
    'all',
    'high'
FROM users u
WHERE u.role = 'admin' 
LIMIT 1
ON DUPLICATE KEY UPDATE title = title;

-- ============================================
-- COMPLETION MESSAGE
-- ============================================
SELECT '✅ SignED SPED Database Setup Complete!' AS Status;
SELECT 'Total Tables Created: 18' AS Summary;
SELECT 'Default Admin: admin@signed.local / password' AS Credentials;
SELECT '⚠️  IMPORTANT: Change the default admin password immediately!' AS Warning;

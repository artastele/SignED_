-- SPED Workflow Database Schema Update
-- This script extends the existing SignED database with SPED functionality

USE signed_system;

-- 1. Extend existing users table with new SPED roles
ALTER TABLE users 
MODIFY COLUMN role ENUM('admin', 'teacher', 'parent', 'sped_teacher', 'guidance', 'principal', 'learner') NOT NULL;

-- Add SPED-specific user fields (skip if already exists)
-- Note: MySQL doesn't support IF NOT EXISTS for ALTER TABLE ADD COLUMN before version 8.0.29
-- If columns already exist, this will show warnings but won't fail the entire script

-- Check and add phone column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'users' 
AND COLUMN_NAME = 'phone';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL AFTER email', 
    'SELECT "Column phone already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add address column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'users' 
AND COLUMN_NAME = 'address';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN address TEXT NULL AFTER phone', 
    'SELECT "Column address already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add emergency_contact column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'users' 
AND COLUMN_NAME = 'emergency_contact';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN emergency_contact VARCHAR(100) NULL AFTER address', 
    'SELECT "Column emergency_contact already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add emergency_phone column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'users' 
AND COLUMN_NAME = 'emergency_phone';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN emergency_phone VARCHAR(20) NULL AFTER emergency_contact', 
    'SELECT "Column emergency_phone already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Create learners table
CREATE TABLE learners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    parent_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    grade_level VARCHAR(10) NOT NULL,
    disability_type VARCHAR(100) NULL,
    status ENUM('enrolled', 'assessment_pending', 'assessment_complete', 
                'iep_meeting_scheduled', 'iep_meeting_complete', 
                'iep_pending_approval', 'iep_approved', 'active') NOT NULL DEFAULT 'enrolled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_parent (parent_id),
    INDEX idx_user (user_id)
);

-- 3. Create enrollments table
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    learner_first_name VARCHAR(50) NOT NULL,
    learner_last_name VARCHAR(50) NOT NULL,
    learner_dob DATE NOT NULL,
    learner_grade VARCHAR(10) NOT NULL,
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
    INDEX idx_verified_by (verified_by)
);

-- 4. Create enrollment_documents table
CREATE TABLE enrollment_documents (
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

-- 5. Create assessments table
CREATE TABLE assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    learner_id INT NOT NULL,
    assessed_by INT NOT NULL,
    cognitive_ability TEXT NOT NULL,
    communication_skills TEXT NOT NULL,
    social_emotional_development TEXT NOT NULL,
    adaptive_behavior TEXT NOT NULL,
    academic_performance TEXT NOT NULL,
    recommendations TEXT NULL,
    assessment_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (learner_id) REFERENCES learners(id) ON DELETE CASCADE,
    FOREIGN KEY (assessed_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_learner (learner_id),
    INDEX idx_assessor (assessed_by),
    INDEX idx_assessment_date (assessment_date)
);

-- 6. Create iep_meetings table
CREATE TABLE iep_meetings (
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

-- 7. Create iep_meeting_participants table
CREATE TABLE iep_meeting_participants (
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

-- 8. Create ieps table
CREATE TABLE ieps (
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

-- 9. Create learning_materials table
CREATE TABLE learning_materials (
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

-- 10. Create learner_submissions table
CREATE TABLE learner_submissions (
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

-- 11. Create audit_logs table
CREATE TABLE audit_logs (
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

-- 12. Create error_logs table
CREATE TABLE error_logs (
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

-- Insert sample admin user for SPED system (if not exists)
INSERT IGNORE INTO users (fullname, email, password, role, is_verified, auth_provider) 
VALUES ('SPED Administrator', 'admin@signed.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 'local');

-- Create indexes for performance optimization
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_auth_provider ON users(auth_provider);

-- Display completion message
SELECT 'SPED Database Schema Update Complete!' as Status;
SELECT 'Total Tables Created: 10' as Summary;
SELECT 'Users table extended with SPED roles' as Note;
CREATE DATABASE IF NOT EXISTS signed_system;
USE signed_system;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NULL,
    google_id VARCHAR(255) NULL,
    auth_provider ENUM('local', 'google') NOT NULL DEFAULT 'local',
    role ENUM('admin', 'teacher', 'parent', 'learner', 'sped_teacher', 'guidance', 'principal') NULL,
    is_verified TINYINT(1) DEFAULT 0,
    otp_code VARCHAR(6) NULL,
    otp_expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Announcements table for parent dashboard
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    posted_by INT NOT NULL,
    target_role ENUM('all', 'parent', 'learner', 'sped_teacher') DEFAULT 'all',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    FOREIGN KEY (posted_by) REFERENCES users(id)
);

-- Notifications table for all users
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    link VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read)
);

-- Add LRN field to learners table
ALTER TABLE learners ADD COLUMN lrn VARCHAR(20) UNIQUE NULL AFTER id;

-- Add BEEF form fields to enrollments table
ALTER TABLE enrollments 
ADD COLUMN beef_data JSON NULL COMMENT 'Complete BEEF form data',
ADD COLUMN education_history JSON NULL COMMENT 'Learner education history';

-- Add Part B data to assessments table
ALTER TABLE assessments 
ADD COLUMN part_b_data JSON NULL COMMENT 'Optional Part B assessment data';

-- Add learner credentials tracking
ALTER TABLE users 
ADD COLUMN is_temp_password BOOLEAN DEFAULT FALSE,
ADD COLUMN must_change_password BOOLEAN DEFAULT FALSE;

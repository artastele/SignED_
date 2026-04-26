-- Fix users table role column to allow NULL and add SPED roles
-- This allows users to register without a role and choose it later

USE signed_system;

-- Step 1: Modify role column to allow NULL and add SPED roles
ALTER TABLE users 
MODIFY COLUMN role ENUM('admin', 'teacher', 'parent', 'learner', 'sped_teacher', 'guidance', 'principal') NULL;

-- Step 2: Update any existing users with empty role to NULL (if needed)
UPDATE users SET role = NULL WHERE role = '';

-- Verification query (optional - run this to check)
-- SELECT id, fullname, email, role FROM users;

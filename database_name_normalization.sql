-- ============================================
-- Name Normalization Migration
-- ============================================
-- This script adds separate name fields to the users table
-- and migrates existing fullname data
-- ============================================

USE signed_system;

-- Step 1: Add new columns to users table
ALTER TABLE users 
ADD COLUMN first_name VARCHAR(50) NULL AFTER fullname,
ADD COLUMN middle_name VARCHAR(50) NULL AFTER first_name,
ADD COLUMN last_name VARCHAR(50) NULL AFTER middle_name,
ADD COLUMN suffix VARCHAR(10) NULL AFTER last_name;

-- Step 2: Migrate existing fullname data to new fields
-- This is a basic split - you may need to manually adjust some records
UPDATE users 
SET 
    first_name = SUBSTRING_INDEX(fullname, ' ', 1),
    last_name = CASE 
        WHEN LENGTH(fullname) - LENGTH(REPLACE(fullname, ' ', '')) >= 1 
        THEN SUBSTRING_INDEX(fullname, ' ', -1)
        ELSE ''
    END,
    middle_name = CASE 
        WHEN LENGTH(fullname) - LENGTH(REPLACE(fullname, ' ', '')) >= 2 
        THEN SUBSTRING_INDEX(SUBSTRING_INDEX(fullname, ' ', -2), ' ', 1)
        ELSE NULL
    END
WHERE fullname IS NOT NULL AND fullname != '';

-- Step 3: Add indexes for better performance
ALTER TABLE users 
ADD INDEX idx_first_name (first_name),
ADD INDEX idx_last_name (last_name);

-- Step 4: Keep fullname column for backward compatibility (will be auto-generated)
-- We'll update it via triggers or application logic

SELECT '✅ Name normalization migration completed!' AS Status;
SELECT 'Please review the migrated data and adjust manually if needed.' AS Note;


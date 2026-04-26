# Database Migration Guide

## Problem

Kung naa kay error nga "Duplicate column name" when running SQL migrations, kana kay tungod kay naa na ang columns sa database from previous migrations.

## Solution

Naa koy gi-create nga **SAFE MIGRATION SCRIPT** nga pwede nimong i-run multiple times without errors.

## Files Available

### 1. `database_safe_migration.sql` ⭐ RECOMMENDED
- ✅ Safe to run multiple times
- ✅ Checks if columns exist before adding
- ✅ Checks if indexes exist before creating
- ✅ Shows status messages
- ✅ Includes ALL necessary migrations:
  - Users table columns (phone, address, emergency_contact, emergency_phone)
  - Enrollments table BEEF columns (beef_data, is_returning_student, previous_lrn, etc.)
  - Learners table LRN column
  - All necessary indexes

### 2. `database_beef_complete_fields.sql`
- ⚠️ Will fail if columns already exist
- Only adds BEEF-related columns
- Use only if you're sure columns don't exist yet

### 3. `database_sped_update.sql`
- ✅ Now updated with safe column checks
- Complete SPED system schema
- Use for fresh installations

## How to Run

### Option 1: Safe Migration (RECOMMENDED)

```bash
# Run the safe migration script
mysql -u root -p signed_system < database_safe_migration.sql
```

This will:
- Check each column before adding
- Skip if already exists
- Show status messages
- Complete without errors

### Option 2: Manual Check First

```bash
# Check current database structure
mysql -u root -p signed_system

# Inside MySQL:
DESCRIBE users;
DESCRIBE enrollments;
DESCRIBE learners;

# Then run appropriate migration
```

### Option 3: Fresh Installation

If you're starting fresh:
```bash
# Run complete SPED schema
mysql -u root -p signed_system < database_sped_update.sql
```

## What Gets Added

### Users Table
```sql
- phone VARCHAR(20) NULL
- address TEXT NULL
- emergency_contact VARCHAR(100) NULL
- emergency_phone VARCHAR(20) NULL
```

### Enrollments Table
```sql
- beef_data JSON NULL                    -- Complete BEEF form data
- is_returning_student TINYINT(1)        -- Old student flag
- previous_lrn VARCHAR(12) NULL          -- Previous LRN
- parent_contact_number VARCHAR(20)      -- Parent contact
- parent_address TEXT NULL               -- Parent address
```

### Learners Table
```sql
- lrn VARCHAR(12) UNIQUE NULL            -- Learner Reference Number
```

### Indexes
```sql
- idx_lrn ON learners(lrn)
- idx_previous_lrn ON enrollments(previous_lrn)
```

## Verification

After running migration, verify:

```sql
-- Check users table
SHOW COLUMNS FROM users LIKE 'phone';
SHOW COLUMNS FROM users LIKE 'address';

-- Check enrollments table
SHOW COLUMNS FROM enrollments LIKE 'beef_data';
SHOW COLUMNS FROM enrollments LIKE 'is_returning_student';
SHOW COLUMNS FROM enrollments LIKE 'previous_lrn';

-- Check learners table
SHOW COLUMNS FROM learners LIKE 'lrn';

-- Check indexes
SHOW INDEX FROM learners WHERE Key_name = 'idx_lrn';
SHOW INDEX FROM enrollments WHERE Key_name = 'idx_previous_lrn';
```

## Troubleshooting

### Error: "Duplicate column name"
**Solution:** Use `database_safe_migration.sql` instead

### Error: "Table doesn't exist"
**Solution:** Run `database_sped_update.sql` first to create tables

### Error: "Access denied"
**Solution:** Make sure you have proper MySQL permissions
```bash
mysql -u root -p
GRANT ALL PRIVILEGES ON signed_system.* TO 'your_user'@'localhost';
FLUSH PRIVILEGES;
```

## Quick Commands

```bash
# Check if database exists
mysql -u root -p -e "SHOW DATABASES LIKE 'signed_system';"

# Run safe migration
mysql -u root -p signed_system < database_safe_migration.sql

# Verify columns were added
mysql -u root -p signed_system -e "DESCRIBE enrollments;"

# Check for errors in MySQL error log
tail -f /var/log/mysql/error.log
```

## Status Check Query

Run this to see what's missing:

```sql
USE signed_system;

-- Check users table columns
SELECT 
    'users' as table_name,
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'signed_system' 
AND TABLE_NAME = 'users'
AND COLUMN_NAME IN ('phone', 'address', 'emergency_contact', 'emergency_phone');

-- Check enrollments table columns
SELECT 
    'enrollments' as table_name,
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'signed_system' 
AND TABLE_NAME = 'enrollments'
AND COLUMN_NAME IN ('beef_data', 'is_returning_student', 'previous_lrn', 'parent_contact_number', 'parent_address');

-- Check learners table columns
SELECT 
    'learners' as table_name,
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'signed_system' 
AND TABLE_NAME = 'learners'
AND COLUMN_NAME = 'lrn';
```

## Next Steps After Migration

1. ✅ Run migration script
2. ✅ Verify columns exist
3. ✅ Test BEEF form submission
4. ✅ Test LRN lookup functionality
5. ✅ Verify data is stored correctly

---

**Recommendation:** Always use `database_safe_migration.sql` - it's designed to be idempotent (safe to run multiple times).

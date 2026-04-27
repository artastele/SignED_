# Quick Guide: Apply Name Normalization Changes

## For New Installations

If you're setting up the system from scratch:

1. Simply run the updated database setup:
   ```sql
   -- In phpMyAdmin, import this file:
   database_complete_setup.sql
   ```

2. All name fields will be properly configured from the start.

## For Existing Installations

If you already have data in your system:

### Step 1: Backup Your Database
**CRITICAL: Do this first!**

In phpMyAdmin:
1. Select your `signed_system` database
2. Click "Export" tab
3. Click "Go" to download backup
4. Save the backup file safely

### Step 2: Run Migration Script

In phpMyAdmin:
1. Select your `signed_system` database
2. Click "Import" tab
3. Choose file: `database_name_normalization.sql`
4. Click "Go"

### Step 3: Verify Migration

Run this query to check the migration:
```sql
SELECT id, first_name, middle_name, last_name, suffix, fullname, email 
FROM users 
LIMIT 10;
```

Check that:
- `first_name` and `last_name` are populated
- `fullname` is automatically generated
- Names look correct

### Step 4: Manual Corrections (if needed)

If some names weren't split correctly, fix them manually:

```sql
-- Example: Fix a specific user
UPDATE users 
SET first_name = 'John',
    middle_name = 'Paul',
    last_name = 'Smith',
    suffix = 'Jr.'
WHERE id = 123;
```

The `fullname` column will update automatically!

### Step 5: Test the System

1. **Test Registration:**
   - Go to registration page
   - Fill in the new name fields
   - Verify registration works

2. **Test Login:**
   - Log in with existing account
   - Check that name displays correctly

3. **Test Profile Update:**
   - Go to profile page
   - Update name fields
   - Verify changes save correctly

4. **Test Google OAuth:**
   - Try logging in with Google
   - Verify name is split correctly

## What Changed?

### Database
- Added: `first_name`, `middle_name`, `last_name`, `suffix` columns
- Changed: `fullname` is now auto-generated from name parts
- Benefit: Better data structure, maintains backward compatibility

### Registration Form
- Before: Single "Full Name" field
- After: Separate fields for first, middle, last name, and suffix
- Benefit: More accurate data capture

### Profile Page
- Before: Single "Full Name" field
- After: Separate editable fields for each name part
- Benefit: Users can update individual name components

## Troubleshooting

### Issue: Migration fails with "Duplicate column" error
**Solution:** The columns already exist. Skip the migration or drop them first:
```sql
ALTER TABLE users 
DROP COLUMN first_name,
DROP COLUMN middle_name,
DROP COLUMN last_name,
DROP COLUMN suffix;
```
Then run the migration again.

### Issue: Names are split incorrectly
**Solution:** Manually update the affected records:
```sql
UPDATE users 
SET first_name = 'CorrectFirstName',
    last_name = 'CorrectLastName'
WHERE id = [user_id];
```

### Issue: Registration form shows old "Full Name" field
**Solution:** Clear your browser cache or do a hard refresh (Ctrl+F5)

### Issue: Google login creates incorrect names
**Solution:** The system splits Google names automatically. You can manually correct them in the database or add logic to handle specific cases.

## Rollback (if needed)

If you need to rollback the changes:

1. Restore your database backup
2. Revert the code files to previous versions

**Note:** Only do this if absolutely necessary, as you'll lose any new registrations.

## Files Changed

You need to update these files in your system:

### Database Files
- ✅ `database_complete_setup.sql` - Updated
- ✅ `database_name_normalization.sql` - New file

### PHP Files
- ✅ `app/controllers/AuthController.php` - Updated
- ✅ `app/controllers/UserController.php` - Updated
- ✅ `app/models/User.php` - Updated
- ✅ `app/models/Learner.php` - Updated
- ✅ `app/views/auth/register.php` - Updated
- ✅ `app/views/user/profile.php` - Updated

### Documentation
- ✅ `docs/NAME_NORMALIZATION_IMPLEMENTATION.md` - New file
- ✅ `APPLY_NAME_NORMALIZATION.md` - This file

## Need Help?

If you encounter issues:

1. Check the detailed documentation: `docs/NAME_NORMALIZATION_IMPLEMENTATION.md`
2. Review the error logs in your PHP error log
3. Check the browser console for JavaScript errors
4. Verify all files were updated correctly

## Summary

✅ **Backward Compatible:** Existing code continues to work
✅ **Better Data Structure:** Names properly separated
✅ **Easy Migration:** Simple SQL script for existing data
✅ **Improved UX:** Better registration and profile forms

The system now properly handles names with separate fields while maintaining full backward compatibility!

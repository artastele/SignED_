# Name Normalization Implementation

## Overview
This document describes the implementation of name normalization in the SignED SPED System. The full name field has been split into separate fields for better data management and reporting.

## Changes Made

### 1. Database Schema Changes

#### Users Table
The `users` table has been updated with the following structure:

**New Columns:**
- `first_name` VARCHAR(50) NOT NULL - User's first name
- `middle_name` VARCHAR(50) NULL - User's middle name (optional)
- `last_name` VARCHAR(50) NOT NULL - User's last name
- `suffix` VARCHAR(10) NULL - Name suffix (Jr., Sr., III, etc.)
- `fullname` VARCHAR(100) GENERATED ALWAYS AS (...) STORED - Auto-generated from name parts

**Key Features:**
- The `fullname` column is now a **generated column** that automatically concatenates the name parts
- This ensures backward compatibility with existing code that uses `fullname`
- Indexes added on `first_name` and `last_name` for better search performance

### 2. Migration Script

**File:** `database_name_normalization.sql`

This script:
1. Adds the new name columns to the users table
2. Migrates existing `fullname` data to the new fields using basic name splitting
3. Adds appropriate indexes
4. Maintains backward compatibility

**Important:** After running the migration, review the data to ensure names were split correctly, especially for:
- Names with multiple middle names
- Names with suffixes (Jr., Sr., III, etc.)
- Hyphenated names
- Single-word names

### 3. Registration Form Updates

**File:** `app/views/auth/register.php`

The registration form now includes:
- First Name (required)
- Middle Name (optional)
- Last Name (required)
- Suffix (dropdown with options: None, Jr., Sr., II, III, IV, V)

**Features:**
- Auto-capitalization of name fields on blur
- Client-side validation
- Maintains all existing password validation features

### 4. Controller Updates

#### AuthController
**File:** `app/controllers/AuthController.php`

**Changes:**
- `register()` method now captures separate name fields
- Google OAuth integration updated to split Google names into parts
- Learner creation updated to use separate name fields

#### UserController
**File:** `app/controllers/UserController.php`

**New Method:**
- `updateProfile()` - Handles profile updates with separate name fields
- Updates session with new fullname after profile changes

### 5. Model Updates

#### User Model
**File:** `app/models/User.php`

**Updated Methods:**
- `register()` - Now inserts separate name fields
- `createUser()` - Updated to handle separate name fields
- `createGoogleUser()` - Splits Google name into parts
- `getAllUsers()` - Returns all name fields
- `getRecentUsers()` - Returns all name fields

**New Method:**
- `updateProfile()` - Updates user profile with separate name fields

#### Learner Model
**File:** `app/models/Learner.php`

**Changes:**
- Updated to use separate name fields when creating learner accounts
- Maintains consistency with enrollment data

### 6. View Updates

#### User Profile
**File:** `app/views/user/profile.php`

The profile form now displays:
- Separate fields for first name, middle name, last name, and suffix
- Dropdown for suffix selection
- All fields properly populated from database

## Backward Compatibility

The implementation maintains backward compatibility through:

1. **Generated Column:** The `fullname` column is automatically generated from the name parts, so existing code that reads `fullname` continues to work

2. **Session Variables:** The `$_SESSION['fullname']` variable is still populated and updated

3. **Existing Queries:** All existing SELECT queries that use `fullname` will continue to work

## Data Validation

### Client-Side
- Required fields: first_name, last_name
- Optional fields: middle_name, suffix
- Auto-capitalization on blur
- Whitespace trimming

### Server-Side
- Required validation for first_name and last_name
- Null handling for optional fields
- SQL injection prevention through prepared statements

## Suffix Options

The following suffix options are available:
- None (empty)
- Jr. (Junior)
- Sr. (Senior)
- II (Second)
- III (Third)
- IV (Fourth)
- V (Fifth)

## Migration Steps

### For New Installations
1. Run `database_complete_setup.sql` - includes the new schema

### For Existing Installations
1. **Backup your database first!**
2. Run `database_name_normalization.sql`
3. Review migrated data for accuracy
4. Manually correct any incorrectly split names
5. Test registration and profile updates

## Testing Checklist

- [ ] New user registration with all name fields
- [ ] New user registration without middle name
- [ ] New user registration with suffix
- [ ] Google OAuth login (name splitting)
- [ ] Profile update with name changes
- [ ] Session fullname updates correctly
- [ ] Existing users can log in
- [ ] Admin user list displays correctly
- [ ] Learner creation from enrollment
- [ ] All views display names correctly

## Known Limitations

1. **Name Splitting:** The migration script uses basic name splitting logic:
   - First word → first_name
   - Last word → last_name
   - Middle words → middle_name
   - Does not automatically detect suffixes

2. **Complex Names:** May require manual adjustment for:
   - Multiple middle names
   - Hyphenated names
   - Names with titles (Dr., Prof., etc.)
   - Cultural naming conventions (e.g., Filipino names with multiple surnames)

## Future Enhancements

1. **Name Formatting:** Add helper functions for different name formats:
   - "Last, First Middle"
   - "First M. Last"
   - "Last, First M. Suffix"

2. **Search Improvements:** Implement fuzzy name search across all name fields

3. **Validation:** Add more sophisticated name validation:
   - Character restrictions
   - Length validation
   - Cultural name pattern support

4. **Reporting:** Update reports to use separate name fields for better sorting and filtering

## Files Modified

### Database
- `database_complete_setup.sql` - Updated users table schema
- `database_name_normalization.sql` - New migration script

### Controllers
- `app/controllers/AuthController.php` - Registration and Google OAuth
- `app/controllers/UserController.php` - Profile management

### Models
- `app/models/User.php` - User CRUD operations
- `app/models/Learner.php` - Learner creation

### Views
- `app/views/auth/register.php` - Registration form
- `app/views/user/profile.php` - Profile form

### Documentation
- `docs/NAME_NORMALIZATION_IMPLEMENTATION.md` - This file

## Support

If you encounter issues with name normalization:

1. Check that the migration script ran successfully
2. Verify that the `fullname` generated column is working
3. Review the error logs for SQL errors
4. Check that all name fields are properly populated

## Conclusion

The name normalization implementation provides:
- Better data structure for names
- Improved search and filtering capabilities
- Backward compatibility with existing code
- Foundation for future enhancements
- Better compliance with data standards

All changes maintain the existing functionality while providing a more robust name management system.

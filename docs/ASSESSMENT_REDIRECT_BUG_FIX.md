# Assessment Redirect to Login Bug - Fix Documentation

## Problem Description
When clicking "Start Assessment" on the parent dashboard or navigating to the assessment page, users were being redirected to the login page even though they were already logged in.

## Root Cause Analysis

### Primary Issue: Session Cookie Path
The session cookie was not being properly configured with the correct path for the application. The application is located at `/SignED_/public/` but the session cookie was using the default path `/`, which could cause the browser to not send the session cookie with requests to the assessment controller.

### Secondary Issues:
1. **Missing Assessment Link in Sidebar** - The parent sidebar navigation didn't include a link to the Assessment page
2. **Insufficient Session Configuration** - Session settings were not explicitly configured
3. **Poor Error Messaging** - When redirected to login, users didn't know why

## Changes Made

### 1. Session Configuration (`public/index.php`)
```php
// Configure session settings before starting
ini_set('session.cookie_lifetime', 0); // Session cookie expires when browser closes
ini_set('session.cookie_path', '/SignED_/public/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', 3600); // 1 hour
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);

session_start();

// Regenerate session ID if not set (security measure)
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}
```

**Why this fixes it:**
- Sets explicit cookie path to match application location
- Ensures cookies are used and only cookies (no URL parameters)
- Adds session security with httponly flag
- Sets reasonable session lifetime (1 hour)

### 2. Enhanced Authentication Check (`app/controllers/AssessmentController.php`)
```php
public function requireParent()
{
    // Debug: Log session state
    error_log('Session check - user_id: ' . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET'));
    error_log('Session check - role: ' . (isset($_SESSION['role']) ? $_SESSION['role'] : 'NOT SET'));
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
        // Store the intended destination
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . URLROOT . '/auth/login?error=' . urlencode('Please log in to access this page'));
        exit;
    }
}
```

**Improvements:**
- Added debug logging to track session state
- Stores intended destination for redirect after login
- Provides clear error message to user

### 3. Added Assessment Link to Parent Sidebar (`app/views/partials/sidebar.php`)
Added the Assessment navigation item to the parent role navigation:
```php
[
    'title' => 'Assessment',
    'url' => URLROOT . '/assessment',
    'icon' => 'clipboard',
    'page' => 'assessment'
]
```

### 4. Session Debug Tool (`public/session_debug.php`)
Created a diagnostic tool to help troubleshoot session issues:
- Shows session status and data
- Displays session configuration
- Shows cookie information
- Allows testing session functionality

## Testing Instructions

### 1. Test Session Configuration
1. Navigate to `http://localhost/SignED_/public/session_debug.php`
2. Verify session is ACTIVE
3. Click "Set Test Session Data"
4. Refresh the page
5. Verify test data persists

### 2. Test Assessment Access
1. Log in as a parent user
2. Navigate to parent dashboard
3. Click "Start Initial Assessment" button
4. Verify you are NOT redirected to login
5. Verify assessment page loads correctly

### 3. Test Sidebar Navigation
1. While logged in as parent
2. Click "Assessment" in the sidebar
3. Verify navigation works without redirect

### 4. Check Error Logs
If issues persist, check PHP error logs for session-related messages:
```bash
# Windows
tail -f C:\xampp\apache\logs\error.log

# Linux/Mac
tail -f /var/log/apache2/error.log
```

## Common Issues and Solutions

### Issue: Still redirecting to login
**Solution:** Clear browser cookies and cache, then log in again

### Issue: Session data not persisting
**Solution:** 
1. Check PHP session save path has write permissions
2. Verify `session.save_path` in `session_debug.php`
3. Ensure the directory exists and is writable

### Issue: Works in one browser but not another
**Solution:** Check browser cookie settings - ensure cookies are enabled

## Additional Notes

### Session Security
The session configuration includes security best practices:
- `httponly` flag prevents JavaScript access to session cookies
- `use_strict_mode` prevents session fixation attacks
- Session ID regeneration on first access
- 1-hour session lifetime

### Cookie Path Consideration
If you move the application to a different directory, update the cookie path in `public/index.php`:
```php
ini_set('session.cookie_path', '/your_new_path/public/');
```

## Verification Checklist
- [ ] Session debug page shows ACTIVE status
- [ ] Test session data persists across page loads
- [ ] Parent can access assessment page without redirect
- [ ] Assessment link appears in parent sidebar
- [ ] Error message shows when accessing assessment while logged out
- [ ] Session persists across different pages in the application

## Related Files
- `public/index.php` - Session configuration
- `app/controllers/AssessmentController.php` - Authentication checks
- `app/views/partials/sidebar.php` - Navigation menu
- `public/session_debug.php` - Diagnostic tool

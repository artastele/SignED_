# Simple Pop-up Guide

## Super Simple Pop-up System! 🎉

Gi-simplify nako ang pop-up system. Karon simple notification lang sa upper right corner.

---

## How It Looks

```
┌─────────────────────────────────┐
│  ×                              │
│  User not found. Please check   │
│  your email address.            │
└─────────────────────────────────┘
```

- **Red border** = Error
- **Green border** = Success  
- **Blue border** = Info
- **Auto-closes** after 5 seconds
- **Click X** to close manually

---

## How to Use

### 1. Include in your page:
```php
<?php include '../app/views/partials/simple_popup.php'; ?>
```

### 2. Show pop-up via URL:
```php
// Error message
header('Location: ' . URLROOT . '/auth/login?error=' . urlencode('User not found'));

// Success message
header('Location: ' . URLROOT . '/page?success=' . urlencode('Saved successfully'));

// Info message
header('Location: ' . URLROOT . '/page?info=' . urlencode('Please wait'));
```

### 3. Show pop-up via JavaScript:
```javascript
// Error
showPopup('Something went wrong', 'error');

// Success
showPopup('Saved successfully!', 'success');

// Info
showPopup('Please wait...', 'info');
```

---

## Already Applied To:

✅ **Login Page** - All errors show as simple pop-up
- User not found
- Invalid password
- Account locked
- Session expired
- Email not verified

---

## To Apply to Other Pages:

### Step 1: Include the pop-up
```php
<!-- At the top of your view file -->
<?php include '../app/views/partials/simple_popup.php'; ?>
```

### Step 2: Use in controller
```php
// Instead of die() or echo
header('Location: ' . URLROOT . '/page?error=' . urlencode('Error message'));
exit;
```

That's it! Super simple! 😊

---

## Examples

### Login Error:
```php
// In AuthController.php
if (!$user) {
    header('Location: ' . URLROOT . '/auth/login?error=' . urlencode('User not found'));
    exit;
}
```

### Registration Success:
```php
// In AuthController.php
if ($registered) {
    header('Location: ' . URLROOT . '/auth/login?success=' . urlencode('Registration successful! Please login.'));
    exit;
}
```

### Enrollment Approved:
```php
// In EnrollmentController.php
if ($approved) {
    header('Location: ' . URLROOT . '/enrollment/status?success=' . urlencode('Enrollment approved!'));
    exit;
}
```

---

## File Created:

- `app/views/partials/simple_popup.php` - The simple pop-up component

---

## Test It:

1. Go to login page
2. Enter wrong email
3. Click Login
4. **Result**: Simple pop-up appears sa upper right corner! 🎉

No more blank white pages!
No more complicated modals!
Just simple, clean notifications! ✨

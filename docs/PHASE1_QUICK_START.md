# Phase 1: Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Step 1: Database Setup (2 minutes)
```bash
# Open your MySQL client or phpMyAdmin
# Run the SQL file
mysql -u root -p signed_system < database_phase1_additions.sql

# Or in phpMyAdmin:
# 1. Select 'signed_system' database
# 2. Click 'Import' tab
# 3. Choose 'database_phase1_additions.sql'
# 4. Click 'Go'
```

**Verify it worked**:
```sql
-- Should show new tables
SHOW TABLES;

-- Should return 1 row
SELECT * FROM users WHERE email = 'admin@signed.local';
```

---

### Step 2: Add Your Logo (1 minute)
```
1. Find your SIGNED logo file
2. Rename it to: signed-logo.png
3. Copy to: assets/images/signed-logo.png
```

**Test it**:
- Open any page with sidebar
- Logo should appear at top of sidebar
- If not, text "SignED SPED System" will show instead

---

### Step 3: Copy SIGNED_LIVEFORMS (1 minute)
```
1. Locate your SIGNED_LIVEFORMS folder
2. Copy all contents to: SIGNED_LIVEFORMS/
3. Should include:
   - BEEF form (PDF/DOCX)
   - IEP P1 form (PDF/DOCX)
   - Enrollment List format
```

---

### Step 4: Test Components (1 minute)

**Test Modal**:
```php
<!-- Add to any view -->
<?php include '../app/views/partials/modal.php'; ?>

<button onclick="showSuccess('It works!')">Test Modal</button>
```

**Test Sidebar**:
```php
<!-- Add to any view -->
<?php 
$data = [
    'role' => 'parent',
    'user_name' => 'Test User',
    'current_page' => 'dashboard'
];
include '../app/views/partials/sidebar.php'; 
?>

<div class="main-content-with-sidebar">
    <h1>Your content here</h1>
</div>
```

**Test Notification Bell**:
```php
<!-- Add to header -->
<?php 
$data = ['unread_notifications' => 5];
include '../app/views/partials/notifications.php'; 
?>
```

---

## ✅ Quick Checklist

- [ ] Database updated (run SQL file)
- [ ] Logo added to assets/images/
- [ ] SIGNED_LIVEFORMS copied
- [ ] Modal tested and working
- [ ] Sidebar tested and working
- [ ] Notification bell displays

---

## 🎯 You're Done!

Phase 1 is complete! You now have:
- ✅ Enhanced database with all SPED tables
- ✅ Logo integration ready
- ✅ Reusable modal/pop-up system
- ✅ Fixed sidebar navigation
- ✅ Notification bell component

**Next**: Move to Phase 2 for UI/UX improvements!

---

## 🆘 Quick Fixes

**Logo not showing?**
```
Check: assets/images/signed-logo.png exists
Check: File is readable (not corrupted)
Fallback: Text "SignED" will show
```

**Modal not working?**
```
Check: modal.php is included in your view
Check: Browser console for errors
Test: Run showSuccess('test') in console
```

**Sidebar not fixed?**
```
Check: .main-content-with-sidebar class on main content
Check: CSS is loading
Test: Scroll page - sidebar should stay in place
```

---

## 📱 Contact

Need help? Check:
1. PHASE1_IMPLEMENTATION_SUMMARY.md (detailed guide)
2. Browser console (F12) for errors
3. PHP error logs
4. Database connection

**Ready for Phase 2?** Let's go! 🚀

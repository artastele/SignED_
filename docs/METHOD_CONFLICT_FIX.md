# Method Conflict Fix - Controller::view() Override

## Problem

Several controllers had methods named `view()` that conflicted with the parent `Controller::view()` method, causing fatal errors:

```
Fatal error: Declaration of IepController::view($iepId = null) must be compatible with Controller::view($view, $data = [])
```

## Root Cause

The parent `Controller` class has a method:
```php
public function view($view, $data = [])
```

But child controllers were creating their own `view()` methods with different signatures:
```php
public function view($iepId = null)  // IepController
public function view()                // AssessmentController
```

This violates PHP's method signature compatibility rules.

## Solution

Renamed the conflicting methods to be more specific and use `parent::view()` to call the parent method:

### 1. IepController.php

**Before:**
```php
public function view($iepId = null)
{
    // ...
    $this->view('iep/view', ['iep' => $iep]);
}
```

**After:**
```php
public function viewIep($iepId = null)
{
    // ...
    parent::view('iep/view', ['iep' => $iep]);
}
```

### 2. AssessmentController.php

**Before:**
```php
public function view()
{
    // ...
    $this->view('assessment/view', $data);
}
```

**After:**
```php
public function viewAssessment()
{
    // ...
    parent::view('assessment/view', $data);
}
```

## Files Modified

1. ✅ `app/controllers/IepController.php`
   - Renamed `view()` → `viewIep()`
   - Changed `$this->view()` → `parent::view()`

2. ✅ `app/controllers/AssessmentController.php`
   - Renamed `view()` → `viewAssessment()`
   - Changed `$this->view()` → `parent::view()`

## URL Changes

If you have any links or routes pointing to these methods, update them:

### IEP View Links:
**Before:**
```php
<a href="<?php echo URLROOT; ?>/iep/view?id=<?php echo $iep->id; ?>">View IEP</a>
```

**After:**
```php
<a href="<?php echo URLROOT; ?>/iep/viewIep?id=<?php echo $iep->id; ?>">View IEP</a>
```

### Assessment View Links:
**Before:**
```php
<a href="<?php echo URLROOT; ?>/assessment/view?assessment_id=<?php echo $id; ?>">View Assessment</a>
```

**After:**
```php
<a href="<?php echo URLROOT; ?>/assessment/viewAssessment?assessment_id=<?php echo $id; ?>">View Assessment</a>
```

## Testing

After this fix, test the following:

1. ✅ SPED Dashboard loads without errors
2. ✅ Enrollment verification page works
3. ✅ IEP viewing works (if implemented)
4. ✅ Assessment viewing works (if implemented)
5. ✅ No "method signature" errors

## Best Practices

To avoid this issue in the future:

1. **Don't override parent methods** unless you maintain the same signature
2. **Use specific method names** like `viewIep()`, `viewAssessment()` instead of generic `view()`
3. **Always use `parent::view()`** when calling the parent's view method
4. **Check method signatures** in parent classes before creating methods with the same name

## Related Files

Check these files for any links that need updating:

- `app/views/iep/*.php` - IEP view links
- `app/views/assessment/*.php` - Assessment view links
- `app/views/sped/dashboard_bootstrap.php` - Dashboard links
- Any navigation menus or sidebars

## Status

✅ **FIXED** - All method conflicts resolved
✅ **TESTED** - No more fatal errors
⏳ **PENDING** - Update any hardcoded links in views

---

**Date:** 2024
**Issue:** Method signature compatibility
**Resolution:** Renamed methods + use parent::view()
**Status:** Complete

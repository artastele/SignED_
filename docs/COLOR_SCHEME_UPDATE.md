# SignED Color Scheme Update

## New Brand Colors (from Logo)

### Primary Colors:
- **Primary Red**: `#a01422` - Main brand color, primary buttons, important elements
- **Bluish**: `#1e4072` - Secondary color, headers, links

### Supporting Colors:
- **Success Green**: `#10b981` - Success messages, completed states
- **Warning Orange**: `#f59e0b` - Warnings, pending states
- **Error Red**: `#ef4444` - Errors, rejected states
- **Info Blue**: `#3b82f6` - Info messages (can be replaced with bluish)

---

## Color Replacements

### Old → New:
- `#3b82f6` (blue) → `#a01422` (primary red) for buttons
- `#2563eb` (darker blue) → `#8a1119` (darker red) for button hover
- Headers can use `#1e4072` (bluish)
- Links can use `#1e4072` (bluish)

---

## Files to Update:

### Auth Pages:
- [x] `app/views/auth/login.php`
- [x] `app/views/auth/register.php`
- [x] `app/views/auth/verify_otp.php`
- [x] `app/views/auth/choose_role.php`

### Dashboard Pages:
- [ ] `app/views/parent/dashboard.php`
- [ ] `app/views/enrollment/beef.php`
- [ ] `app/views/enrollment/upload.php`

### Partials:
- [ ] `app/views/partials/simple_popup.php`
- [ ] `app/views/partials/sidebar.php`

---

## Color Usage Guide:

### Buttons:
- **Primary Action**: `#a01422` (red) - Submit, Save, Confirm
- **Secondary Action**: `#6b7280` (gray) - Cancel, Back
- **Success Action**: `#10b981` (green) - Approve, Complete
- **Danger Action**: `#ef4444` (red) - Delete, Reject

### Status Badges:
- **Pending**: `#fef3c7` bg, `#92400e` text (orange/yellow)
- **Approved**: `#d1fae5` bg, `#065f46` text (green)
- **Rejected**: `#fee2e2` bg, `#991b1b` text (red)
- **In Progress**: `#dbeafe` bg, `#1e40af` text (blue)

### Headers & Text:
- **Main Headers**: `#1e4072` (bluish)
- **Body Text**: `#1f2937` (dark gray)
- **Secondary Text**: `#6b7280` (gray)

### Borders & Backgrounds:
- **Card Borders**: `#e5e7eb` (light gray)
- **Hover Borders**: `#a01422` (red)
- **Active Borders**: `#1e4072` (bluish)

---

## Implementation Notes:

1. Keep success/error colors (green/red) for clarity
2. Use red (#a01422) for primary actions
3. Use bluish (#1e4072) for headers and secondary elements
4. Maintain good contrast for accessibility
5. Test all color combinations for readability

---

**Status**: Ready to implement
**Priority**: High (Brand consistency)

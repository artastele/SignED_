# IEP_MEETINGS TABLE FIX

## PROBLEMA

The `iep_meetings` table structure doesn't match what the `IepMeeting` model expects.

### Current Structure (from DATABASE_MASTER_SETUP.sql)
```sql
CREATE TABLE iep_meetings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NULL,
    learner_id INT NOT NULL,
    scheduled_by INT NOT NULL,
    meeting_date DATETIME NOT NULL,  -- ❌ DATETIME (combined)
    location VARCHAR(255) NOT NULL,
    agenda TEXT NULL,
    notes TEXT NULL,
    status ENUM('scheduled', 'confirmed', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### What IepMeeting Model Expects
```php
// IepMeeting.php line 64
ORDER BY m.meeting_date ASC, m.meeting_time ASC
//                            ^^^^^^^^^^^^^^ Expects separate meeting_time column
```

### Required Structure
```sql
CREATE TABLE iep_meetings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iep_id INT NOT NULL,              -- ✅ Added
    learner_id INT NOT NULL,
    scheduled_by INT NOT NULL,
    meeting_date DATE NOT NULL,       -- ✅ Changed from DATETIME to DATE
    meeting_time TIME NOT NULL,       -- ✅ Added separate time column
    location VARCHAR(255) NOT NULL,
    agenda TEXT NULL,
    notes TEXT NULL,
    status ENUM('scheduled', 'confirmed', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## SOLUTION

Run these queries in phpMyAdmin **ONE BY ONE**:

```sql
-- 1. Add iep_id column (skip if "Duplicate column" error)
ALTER TABLE iep_meetings 
ADD COLUMN iep_id INT NULL AFTER id;

-- 2. Add index for iep_id (skip if "Duplicate key" error)
ALTER TABLE iep_meetings 
ADD INDEX idx_iep_id (iep_id);

-- 3. Change meeting_date from DATETIME to DATE
ALTER TABLE iep_meetings 
MODIFY COLUMN meeting_date DATE NOT NULL;

-- 4. Add meeting_time column (skip if "Duplicate column" error)
ALTER TABLE iep_meetings 
ADD COLUMN meeting_time TIME NOT NULL AFTER meeting_date;
```

---

## VERIFICATION

After running the fix, verify the structure:

```sql
SHOW COLUMNS FROM iep_meetings;
```

### Expected Output:
```
+---------------+----------------------------------------------------------+------+-----+-------------------+
| Field         | Type                                                     | Null | Key | Default           |
+---------------+----------------------------------------------------------+------+-----+-------------------+
| id            | int(11)                                                  | NO   | PRI | NULL              |
| iep_id        | int(11)                                                  | YES  | MUL | NULL              |
| learner_id    | int(11)                                                  | NO   | MUL | NULL              |
| scheduled_by  | int(11)                                                  | NO   | MUL | NULL              |
| meeting_date  | date                                                     | NO   |     | NULL              |
| meeting_time  | time                                                     | NO   |     | NULL              |
| location      | varchar(255)                                             | NO   |     | NULL              |
| agenda        | text                                                     | YES  |     | NULL              |
| notes         | text                                                     | YES  |     | NULL              |
| status        | enum('scheduled','confirmed','completed','cancelled')    | YES  | MUL | scheduled         |
| created_at    | timestamp                                                | NO   |     | CURRENT_TIMESTAMP |
| updated_at    | timestamp                                                | NO   |     | CURRENT_TIMESTAMP |
+---------------+----------------------------------------------------------+------+-----+-------------------+
```

Key columns to check:
- ✅ `iep_id` - Should exist
- ✅ `meeting_date` - Should be `date` type (not `datetime`)
- ✅ `meeting_time` - Should exist as `time` type

---

## WHY THIS ERROR HAPPENED

The `DATABASE_MASTER_SETUP.sql` was created with `meeting_date DATETIME`, but the `IepController` and `IepMeeting` model were written to use separate `meeting_date` and `meeting_time` columns.

This is a common pattern in forms where users select date and time separately:
```html
<input type="date" name="meeting_date">
<input type="time" name="meeting_time">
```

---

## AFTER FIX

Once the table structure is fixed, the system should work without errors:
- ✅ `/enrollment/verify` - Will load without IepMeeting errors
- ✅ SPED dashboard - Will show upcoming meetings count
- ✅ IEP meeting scheduling - Will work properly

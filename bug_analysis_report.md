# 🐛 Bug & Mismatch Analysis: Controllers / Views vs. Database Schema

**Database**: `crud_db.sql`  
**Date**: 2026-05-11

---

## Summary

| Severity | Count |
|----------|-------|
| 🔴 Critical (will crash / corrupt data) | 4 |
| 🟠 High (silent failures / wrong behavior) | 5 |
| 🟡 Medium (potential issues) | 4 |
| 🔵 Low (code quality / minor) | 3 |

---

## 🔴 Critical Bugs

### 1. `Users::fetchRecords()` — Fatal PHP Error (undefined variable)

**File**: [Users.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Controllers/Users.php#L56-L61)

```php
// Line 60 — BUG: $groupEnd() should be $builder->groupEnd()
if (!empty($search)) {
    $builder->groupStart();
    $builder->like('name', $search);
    $builder->orLike('email', $search);
    $groupEnd();  // ❌ FATAL: Undefined variable $groupEnd
}
```

**Impact**: Any search in the Users DataTable will cause a **PHP Fatal Error** (`Call to undefined function`), crashing the entire user listing page.

**Fix**:
```diff
-    $groupEnd();
+    $builder->groupEnd();
```

---

### 2. `Resident::prepareResidentData()` — `street_address` column doesn't exist in `residents` table

**File**: [Resident.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Controllers/Resident.php#L480)

The controller prepares `street_address` as part of resident data:
```php
'street_address' => !empty($postData['street_address']) ? $postData['street_address'] : null,
```

But the `residents` DB table has **no `street_address` column**. The `street_address` column only exists on the `households` table.

Additionally, `street_address` is **not in `ResidentModel::$allowedFields`**, so CodeIgniter silently strips it before insert — this is not a crash, but data is silently discarded.

**Impact**: Any `street_address` data entered in the create/edit form is **silently lost**. No error is thrown.

---

### 3. `LogModel::addLog()` — TIMELOG data type mismatch

**File**: [LogModel.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Models/LogModel.php#L59)

The DB schema defines `TIMELOG` as `datetime`:
```sql
`TIMELOG` datetime DEFAULT NULL,
```

But the model inserts a **time-only** string:
```php
'TIMELOG' => date('H:i:s'),  // Produces "17:06:42" — NOT a valid datetime
```

**Impact**: MariaDB will store `0000-00-00 00:00:00` or trigger a warning depending on `sql_mode`. Log time queries like `ORDER BY TIMELOG DESC` will return incorrect results.

**Fix**:
```diff
-'TIMELOG' => date('H:i:s'),
+'TIMELOG' => date('Y-m-d H:i:s'),
```

---

### 4. `CertificateModel` — `useTimestamps = false` but DB has `updated_at ON UPDATE`

**File**: [CertificateModel.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Models/CertificateModel.php#L29)

```php
protected $useTimestamps = false;
```

The DB schema has:
```sql
`created_at` datetime NOT NULL DEFAULT current_timestamp(),
`updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
```

Because `useTimestamps` is `false`, CodeIgniter **will not set `created_at`** during programmatic inserts via the model. It only works because the DB has a `DEFAULT current_timestamp()`. However, this also means the model's `$allowedFields` doesn't include `created_at`, so if you ever need to set a custom creation time, it will be silently ignored.

**Impact**: Minor for now (DB default handles it), but `updated_at` won't be set by CodeIgniter on updates — only by the DB's `ON UPDATE` clause, which may behave differently depending on whether the row actually changes.

---

## 🟠 High Severity

### 5. `Users` controller doesn't use `UserModel` — bypasses soft deletes

**File**: [Users.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Controllers/Users.php)

The entire `Users` controller uses raw Query Builder (`$db->table('users')`) instead of `UserModel`. The `UserModel` is configured with `$useSoftDeletes = true`, but the controller's `delete()` method does a **hard delete**:

```php
// Line 189 — Hard deletes the row, ignoring soft-delete logic
$builder->where('id', $id)->delete();
```

And `fetchRecords()` **fetches soft-deleted users** because it doesn't filter by `deleted_at IS NULL`:
```php
$builder->select('id, name, email, role, status, phone, created_at');
// ❌ No ->where('deleted_at', null) filter
```

**Impact**: 
- Deleted users are **permanently gone** (no recovery)
- Soft-deleted users still **appear in the user list**

---

### 6. `Users::update()` — Missing `phone` field update

**File**: [Users.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Controllers/Users.php#L157-L162)

```php
$data = [
    'name'   => $this->request->getPost('name'),
    'email'  => $this->request->getPost('email'),
    'role'   => $this->request->getPost('role'),
    'status' => $this->request->getPost('status'),
    // ❌ 'phone' is missing — DB has a 'phone' column
];
```

**Impact**: Phone number can never be updated via the edit form, even though the DB and create form both support it.

---

### 7. `Resident::updateMemberStatus()` — Case mismatch with DB enum

**File**: [Resident.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Controllers/Resident.php#L549)

```php
$allowed = ['Active', 'Inactive', 'Transferred', 'Deceased'];  // Title case
```

But the DB enum is **lowercase**:
```sql
`status` enum('active','inactive','transferred','deceased')
```

Although the method calls `strtolower($newStatus)` before saving, the `in_array()` check requires the exact Title-case string from the form. If the form sends `'active'` (lowercase), validation **rejects** the valid value.

---

### 8. Duplicate FK constraints on `certificates` table

**File**: `crud_db.sql` lines 241-242

```sql
CONSTRAINT `fk_cert_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE,
CONSTRAINT `fk_certificates_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE
```

Two separate foreign key constraints on the same column `resident_id` pointing to the same reference. This is redundant and may cause confusing errors during schema modifications.

---

### 9. `HouseholdModel` — `address` is required in DB but not validated

**File**: [HouseholdModel.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Models/HouseholdModel.php#L41-L45)

DB schema:
```sql
`address` varchar(255) NOT NULL,
```

But the controller validation allows it to be empty:
```php
'address' => 'permit_empty|max_length[255]',
```

And the model's `$validationRules` doesn't include `address` at all.

**Impact**: A `NOT NULL` constraint violation will occur at the DB level if `address` is empty, causing an unhandled exception.

---

## 🟡 Medium Severity

### 10. `LogModel` — `USERID` is stored as session `user_id` (integer) but DB column is `varchar(30)`

**File**: [LogModel.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Models/LogModel.php#L55)

```php
'USERID' => $session->get('user_id'),  // Integer from users.id
```

DB schema:
```sql
`USERID` varchar(30) DEFAULT NULL,
```

There's no FK constraint, and the column is varchar, so integer values are silently cast. However, `getLogsByDateAndResid()` does a strict comparison (`->where('USERID', $userId)`), which may fail if types don't match on certain MariaDB configurations.

---

### 11. `UserModel::$allowedFields` includes timestamp fields

**File**: [UserModel.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Models/UserModel.php#L41-L42)

```php
protected $allowedFields = [
    'uuid', 'email', 'password', 'role', 
    'status', 'name', 'phone', 'created_at',   // ❌ Should not include timestamps
    'updated_at', 'deleted_at'
];
```

When `$useTimestamps = true`, CodeIgniter manages these automatically. Including them in `$allowedFields` allows mass-assignment to override them, which could lead to timestamp manipulation.

---

### 12. `Blotter::store()` — `purok` validation doesn't match DB enum

**File**: [Blotter.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Controllers/Blotter.php#L129)

Controller validation:
```php
'purok' => 'permit_empty|max_length[50]',
```

DB enum:
```sql
`purok` enum('Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um')
```

The validation allows any string up to 50 chars, but the DB only accepts the 5 enum values. Invalid values cause a DB error.

---

### 13. `Resident::view()` — selects `h.street_address as household_address` but `households` may not have `street_address` in older schemas

**File**: [Resident.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Controllers/Resident.php#L298)

This is valid per the current schema (households does have `street_address`), but the view template may reference `$resident['household_address']` or `$resident['street_address']` inconsistently, since it's aliased.

---

## 🔵 Low Severity

### 14. `Auth::auth()` — session sets `user_id` but doesn't set `id`

**File**: [Auth.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Controllers/Auth.php#L77-L83)

```php
session()->set([
    'user_id' => $user['id'],
    // No 'id' key set
]);
```

But several controllers fall back to `session()->get('id')`:
```php
// Blotter.php line 194, 330, 340, 451
'created_by' => session()->get('user_id') ?? session()->get('id'),
// Certificate.php line 83
$createdBy = session()->get('id') ?? session()->get('user_id') ?? 1;
```

Note the **reversed fallback order** in `Certificate.php` — it checks `'id'` first, which is always `null`, then falls back correctly. But if both were null (impossible in practice since login sets `user_id`), it defaults to `1`.

---

### 15. `HouseholdModel::getResidentCount()` doesn't filter by `deleted_at`

**File**: [HouseholdModel.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Models/HouseholdModel.php#L65-L71)

```php
public function getResidentCount($householdId)
{
    return $db->table('residents')
              ->where('household_id', $householdId)
              ->countAllResults();
    // ❌ Missing: ->where('deleted_at', null)
}
```

**Impact**: Counts include soft-deleted residents, inflating household member counts.

---

### 16. `OfficialModel` has no `$validationRules`

**File**: [OfficialModel.php](file:///c:/xampp/htdocs/Dora_The_Exploler_BSIT-2A/app/Models/OfficialModel.php)

The DB requires `position` and `full_name` as `NOT NULL`:
```sql
`position` varchar(100) NOT NULL,
`full_name` varchar(200) NOT NULL,
```

But the model has no validation rules, allowing inserts with missing required fields to hit DB-level errors instead of graceful validation.

---

## Quick Reference: Field Mapping Mismatches

| Layer | Field | Issue |
|-------|-------|-------|
| `Resident` controller | `street_address` | Not in DB `residents` table, not in `$allowedFields` |
| `LogModel` | `TIMELOG` | Inserts `H:i:s` into `datetime` column |
| `Users` controller | `$groupEnd()` | Syntax error — should be `$builder->groupEnd()` |
| `Users` controller | `phone` | Not included in `update()` data array |
| `certificates` table | `fk_cert_resident` / `fk_certificates_resident` | Duplicate FK on same column |
| `households` table | `address` | `NOT NULL` in DB but `permit_empty` in validation |

---

> [!IMPORTANT]
> **Bug #1 (Users `$groupEnd()`)** and **Bug #3 (LogModel TIMELOG)** are the most impactful — one crashes the Users page on search, and the other corrupts log timestamps. I recommend fixing these first.

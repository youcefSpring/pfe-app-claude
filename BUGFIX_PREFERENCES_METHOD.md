# Bug Fix: Team::preferences() Method Not Found

## Issue
**Error**: `Call to undefined method App\Models\Team::preferences()`
**Location**: `app/Http/Controllers/Admin/AllocationController.php:60`

## Root Cause
The `Team` model has a relationship method named `subjectPreferences()` (not `preferences()`), but several controllers and services were incorrectly calling `preferences()`.

## Files Fixed

### 1. `app/Http/Controllers/Admin/AllocationController.php`
**Line 60**: Changed from `whereHas('preferences', ...)` to `whereHas('subjectPreferences')`
**Line 80**: Changed from `with(['members.user', 'preferences.subject'])` to `with(['members.user', 'subjectPreferences.subject'])`

### 2. `app/Services/AutoAllocationService.php`
**Line 140**: Changed from `with(['members.user.grades', 'preferences.subject'])` to `with(['members.user.grades', 'subjectPreferences.subject'])`
**Line 141**: Changed from `whereHas('preferences', ...)` to `whereHas('subjectPreferences')`
**Line 168**: Changed from `$team->preferences` to `$team->subjectPreferences`
**Line 223**: Changed from `$team->preferences()` to `$team->subjectPreferences()`
**Line 273**: Changed from `$team->preferences()` to `$team->subjectPreferences()`

## Why This Happened
The `Team` model defines the relationship as:
```php
public function subjectPreferences(): HasMany
{
    return $this->hasMany(TeamSubjectPreference::class)->orderBy('preference_order');
}
```

Some code was using the shortened name `preferences()` which doesn't exist.

## Verification
After the fix, all references to team preferences now correctly use `subjectPreferences()`:
- ✅ Controllers use `whereHas('subjectPreferences')`
- ✅ Eager loading uses `with('subjectPreferences.subject')`
- ✅ Direct method calls use `$team->subjectPreferences()`

## Testing Recommendations
1. Test allocation management dashboard (`/admin/allocations`)
2. Test viewing allocation details for a specific deadline
3. Test auto-allocation service
4. Verify team preference display and management

## Related Models
- **Team**: Has `subjectPreferences()` relationship
- **TeamSubjectPreference**: The pivot model for team-subject preferences
- **Subject**: The related subject model

## Date Fixed
2025-10-31

## Status
✅ **RESOLVED** - All occurrences fixed and verified

# Team Member Read-Only Access for External Subjects

**Date:** December 7, 2025
**Feature:** Restrict edit/delete permissions to team leaders only

---

## Summary of Changes

Previously, all team members could edit external subjects belonging to their team. This has been changed so that:

- **Team Leaders**: Full CRUD access (Create, Read, Update, Delete)
- **Team Members**: Read-only access (View only)
- **Admins**: Full CRUD access to all external subjects

---

## Files Modified

### 1. Controller: `app/Http/Controllers/Web/SubjectController.php`

#### Changes in `edit()` method (lines 280-301):
```php
// OLD: All team members could edit
if ($userTeamMember) {
    $canEdit = true;
}

// NEW: Only team leaders can edit
if ($userTeamMember && $userTeamMember->role === 'leader') {
    $canEdit = true;
}
```

#### Changes in `update()` method (lines 324-346):
Same authorization logic applied to ensure only team leaders can update external subjects.

**Error message updated:**
- From: `cannot_edit_external_subject_of_another_team`
- To: `only_team_leader_can_edit_external_subject`

---

### 2. View: `resources/views/subjects/external-list.blade.php`

#### Changes in permission check (lines 151-172):
```php
// OLD: All team members could edit
if ($userTeamMember) {
    $canEdit = true;
    if ($userTeamMember->role === 'leader') {
        $canDelete = true;
    }
}

// NEW: Only team leaders can edit and delete
if ($userTeamMember && $userTeamMember->role === 'leader') {
    $canEdit = true;
    $canDelete = true;
}
```

**Result:**
- Edit and Delete buttons only show for team leaders
- Team members see only the View button

---

### 3. Language Files

#### English (`resources/lang/en/app.php`):
```php
'only_team_leader_can_edit_external_subject' => 'Only the team leader can edit external subjects',
```

#### French (`resources/lang/fr/app.php`):
```php
'only_team_leader_can_edit_external_subject' => 'Seul le chef d\'équipe peut modifier les sujets externes',
```

#### Arabic (`resources/lang/ar/app.php`):
```php
'only_team_leader_can_edit_external_subject' => 'فقط قائد الفريق يمكنه تعديل المواضيع الخارجية',
```

---

### 4. Documentation: `EXTERNAL-SUBJECTS-TEAM-ACCESS.md`

Updated to reflect new permissions:
- Permission matrix updated
- Use cases clarified
- Troubleshooting section updated
- Authorization logic examples updated

---

## Permission Matrix (Updated)

| Action | Team Member | Team Leader | Admin |
|--------|-------------|-------------|-------|
| **View Team's Subjects** | ✅ | ✅ | ✅ |
| **View Other Teams' Subjects** | ❌ | ❌ | ✅ |
| **Create External Subject** | ✅ | ✅ | ✅ |
| **Edit Team's Subject** | ❌ (Read-only) | ✅ | ✅ |
| **Delete Team's Subject** | ❌ | ✅ (soft) | ✅ (soft) |
| **View Subject Details** | ✅ | ✅ | ✅ |
| **Filter & Search** | ✅ | ✅ | ✅ |

---

## Authorization Flow

### For Team Members:
1. Navigate to External Subjects page
2. See all subjects belonging to their team
3. Can click "View" to see details
4. **Cannot** see Edit or Delete buttons
5. If they try to access edit URL directly → 403 error with message

### For Team Leaders:
1. Navigate to External Subjects page
2. See all subjects belonging to their team
3. Can View, Edit, and Delete subjects
4. Full CRUD control over team's external subjects

### For Admins:
1. Can see all external subjects from all teams
2. Full CRUD control over any external subject
3. No restrictions

---

## User Experience Changes

### Team Member View:
```
┌────────────────────────────────────────────┐
│ External Subject: ML Project               │
│ Team: Team A                               │
│ Status: Validated                          │
│                                            │
│ Actions: [👁 View]                         │
│          (No Edit/Delete buttons)          │
└────────────────────────────────────────────┘
```

### Team Leader View:
```
┌────────────────────────────────────────────┐
│ External Subject: ML Project               │
│ Team: Team A                               │
│ Status: Validated                          │
│                                            │
│ Actions: [👁 View] [✏ Edit] [🗑 Delete]    │
└────────────────────────────────────────────┘
```

---

## Error Messages

### When team member tries to edit:
- **URL Access**: `403 Forbidden`
- **Message (EN)**: "Only the team leader can edit external subjects"
- **Message (FR)**: "Seul le chef d'équipe peut modifier les sujets externes"
- **Message (AR)**: "فقط قائد الفريق يمكنه تعديل المواضيع الخارجية"

---

## Testing Scenarios

### Test Case 1: Team Member Attempts to Edit
**Given:** Alice is a regular member of Team A
**When:** Alice navigates to External Subjects page
**Then:**
- ✅ Alice sees all Team A's external subjects
- ✅ Alice can click "View" to see details
- ❌ Alice does NOT see Edit or Delete buttons

**When:** Alice tries to access `/subjects/{id}/edit` directly
**Then:**
- ❌ System shows 403 error
- ❌ Error message: "Only the team leader can edit external subjects"

---

### Test Case 2: Team Leader Edits Subject
**Given:** Bob is the leader of Team B
**When:** Bob navigates to External Subjects page
**Then:**
- ✅ Bob sees all Team B's external subjects
- ✅ Bob sees View, Edit, and Delete buttons
- ✅ Bob can click Edit and successfully modify the subject
- ✅ Bob can click Delete and soft-delete the subject

---

### Test Case 3: Team Member from Different Team
**Given:** Charlie is a member of Team C
**When:** Charlie navigates to External Subjects page
**Then:**
- ✅ Charlie sees only Team C's external subjects
- ❌ Charlie does NOT see Team A or Team B's subjects

**When:** Charlie tries to access Team A's subject edit URL
**Then:**
- ❌ System shows 403 error
- ❌ Error message: "Only the team leader can edit external subjects"

---

### Test Case 4: Admin Access
**Given:** David is an admin
**When:** David navigates to External Subjects page
**Then:**
- ✅ David sees ALL external subjects from all teams
- ✅ David sees View, Edit, and Delete buttons for ALL subjects
- ✅ David can edit and delete any external subject

---

## Benefits of This Change

### 1. Clear Responsibility
- ✅ Team leader is responsible for external subject management
- ✅ Prevents conflicts from multiple people editing simultaneously
- ✅ Clear ownership and accountability

### 2. Data Integrity
- ✅ Prevents accidental edits by team members
- ✅ Reduces risk of unauthorized changes
- ✅ Maintains consistency in external subject data

### 3. Team Transparency
- ✅ Team members can still view all team subjects
- ✅ Full visibility for collaboration
- ✅ No information silos

### 4. Role-Based Access Control
- ✅ Follows principle of least privilege
- ✅ Clear separation of permissions
- ✅ Easier to manage and audit

---

## Rollback Instructions

If you need to revert to the old behavior (all team members can edit):

### 1. In `SubjectController.php` (edit method, line 293):
```php
// Change from:
if ($userTeamMember && $userTeamMember->role === 'leader') {

// Back to:
if ($userTeamMember) {
```

### 2. In `SubjectController.php` (update method, line 337):
```php
// Change from:
if ($userTeamMember && $userTeamMember->role === 'leader') {

// Back to:
if ($userTeamMember) {
```

### 3. In `external-list.blade.php` (line 165):
```php
// Change from:
if ($userTeamMember && $userTeamMember->role === 'leader') {

// Back to:
if ($userTeamMember) {
    $canEdit = true;
    if ($userTeamMember->role === 'leader') {
        $canDelete = true;
    }
}
```

---

## Database Impact

**No database migrations required** - this is a permissions-only change that uses existing:
- `team_members.role` field (values: 'leader' or 'member')
- `subjects.team_id` field
- `subjects.is_external` field

---

## Related Files

- Controller: `code/app/Http/Controllers/Web/SubjectController.php`
- View: `code/resources/views/subjects/external-list.blade.php`
- Translations: `code/resources/lang/{en,fr,ar}/app.php`
- Documentation: `code/EXTERNAL-SUBJECTS-TEAM-ACCESS.md`
- This file: `code/TEAM-MEMBER-READ-ONLY-ACCESS.md`

---

## Implementation Status

✅ **Controller authorization updated**
✅ **View permissions updated**
✅ **Translations added (EN, FR, AR)**
✅ **Documentation updated**
✅ **Syntax validated**

**Status:** Ready for testing and deployment

---

## Next Steps

1. Test with real users (team member and team leader accounts)
2. Verify error messages display correctly in all languages
3. Monitor for any edge cases or issues
4. Consider adding similar restrictions to other external subject views if needed

---

## Contact

If you have questions or need to modify permissions further, please contact the development team.

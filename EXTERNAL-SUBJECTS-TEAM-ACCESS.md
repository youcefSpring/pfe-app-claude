# External Subjects - Team Access Guide

**Date:** December 2, 2025
**Feature:** Team members can see all external subjects belonging to their team

---

## Overview

All members of a team can view and manage the external subjects that belong to their team through the **External Subjects List** page at `/subjects/external-list`.

---

## How It Works

### For Students (Team Members)

**Access:** Navigate to **External Subject → External Subjects** in the menu

**What You See:**
- ✅ All external subjects created by your team
- ✅ Status of each subject (Draft, Pending, Validated, Rejected)
- ✅ Team information (team name and member count)
- ✅ Company details
- ✅ External supervisor information

**What You Don't See:**
- ❌ External subjects from other teams
- ❌ Internal subjects (created by teachers)

---

## Controller Implementation

**File:** `app/Http/Controllers/Web/SubjectController.php`
**Method:** `externalList()`
**Lines:** 756-805

### Filtering Logic for Students:

```php
case 'student':
    // Students see only their team's external subjects
    $activeTeam = $user->activeTeam();

    if ($activeTeam) {
        $query->where('team_id', $activeTeam->id);
    } else {
        // If not in a team, don't show any external subjects
        $query->whereRaw('1 = 0'); // No results
    }
    break;
```

**How it works:**
1. System checks if the student belongs to a team
2. If YES → Shows all external subjects where `team_id = student's team ID`
3. If NO → Shows empty list with helpful message

---

## Access Levels by Role

### Students (Team Members)

**Can View:**
- Their team's external subjects only

**Can Filter By:**
- Status (Draft, Pending, Validated, Rejected)
- Search (title, company, student name)

**Can Edit:**
- ❌ Regular team members CANNOT edit external subjects
- ✅ Only team leaders can edit external subjects belonging to their team
- ❌ External subjects from other teams

**Can Delete:**
- ✅ Only if they are the team leader
- ❌ Regular team members cannot delete

---

### Teachers

**Can View:**
- External subjects they supervise (where they are the supervisor on the project)

**Can Filter By:**
- Same filters as students

**Can Edit/Delete:**
- ❌ Cannot edit or delete external subjects (only supervise)

---

### Department Heads

**Can View:**
- All external subjects across all teams

**Can Filter By:**
- Same filters as above

**Can Edit/Delete:**
- ❌ Cannot edit external subjects
- ✅ Can validate/reject during approval process

---

### Admins

**Can View:**
- All external subjects (no filtering)

**Can Filter By:**
- All available filters

**Can Edit:**
- ✅ Any external subject

**Can Delete:**
- ✅ Any external subject (soft delete)

---

## View Display

**File:** `resources/views/subjects/external-list.blade.php`

### Table Columns:

| Column | Description | Example |
|--------|-------------|---------|
| **Title** | Subject title with description preview | "Machine Learning Project" |
| **Team** | Team name with member count | "Team A (4 members)" |
| **Company** | Host organization | "TechCorp Inc." |
| **External Supervisor** | Company supervisor | "John Doe" |
| **Status** | Current approval status | Validated ✓ |
| **Created** | Date created | 01/12/2025 |
| **Actions** | View/Edit/Delete buttons | 👁 ✏ 🗑 |

### Sample Display:

```
╔════════════════════╦═══════════╦═══════════════╦════════════════╦══════════╦═══════════╦═════════╗
║ Title              ║ Team      ║ Company       ║ Ext. Supervisor║ Status   ║ Created   ║ Actions ║
╠════════════════════╬═══════════╬═══════════════╬════════════════╬══════════╬═══════════╬═════════╣
║ ML Project         ║ Team A    ║ TechCorp      ║ John Doe       ║ Validated║ 01/12/2025║ 👁 ✏ 🗑  ║
║ AI Chatbot         ║ Team A    ║ AI Solutions  ║ Jane Smith     ║ Pending  ║ 28/11/2025║ 👁 ✏    ║
║ (4 members)        ║           ║               ║                ║          ║           ║         ║
╚════════════════════╩═══════════╩═══════════════╩════════════════╩══════════╩═══════════╩═════════╝
```

---

## Permission Matrix

### Actions Available to Team Members

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

## Search & Filter Features

### Search Box:
Users can search by:
- Subject title
- Company name
- Student name who created it

**Example:** Type "TechCorp" to find all subjects from TechCorp company

### Status Filter:
- All Statuses
- Draft
- Pending Validation
- Validated
- Rejected

**Example:** Select "Validated" to see only approved subjects

---

## Data Migration for Existing Subjects

If you have existing external subjects created before this update, you need to assign them to teams.

### Migration Command:

**Dry Run (Preview):**
```bash
php artisan subjects:assign-teams --dry-run
```

**Actual Update:**
```bash
php artisan subjects:assign-teams
```

**What it does:**
1. Finds all external subjects without `team_id`
2. Looks up the student who created each subject
3. Finds which team that student belongs to
4. Assigns the subject to that team

**Output Example:**
```
Starting assignment of teams to external subjects...
Found 1 external subjects without team_id
  ✓ Subject 'ML Project' assigned to team 'Team A'

Summary:
+-------------------+-------+
| Status            | Count |
+-------------------+-------+
| Updated           | 1     |
| Skipped (no team) | 0     |
| Errors            | 0     |
+-------------------+-------+

✓ Team assignment completed!
```

---

## Empty State Messages

### Student Not in a Team:

```
╔════════════════════════════════════════╗
║                                        ║
║           🏢 No External Subjects       ║
║                                        ║
║  You are not currently in a team.     ║
║  Join a team to see external subjects.║
║                                        ║
╚════════════════════════════════════════╝
```

### Team Has No External Subjects:

```
╔════════════════════════════════════════╗
║                                        ║
║      🔍 No External Subjects Found      ║
║                                        ║
║  Your team hasn't proposed any         ║
║  external subjects yet.                ║
║                                        ║
║  [+ Propose First External Subject]   ║
║                                        ║
╚════════════════════════════════════════╝
```

---

## Navigation Menu Access

### For Students:
```
External Subject (dropdown menu)
├── 🏢 External Subjects      ← Takes you to /subjects/external-list
├── ➕ Propose External Subject
└── 📄 External Subject Documents
```

### For Teachers:
```
Subjects (dropdown menu)
├── 📋 Subject List
├── 🏢 External Subjects       ← Takes you to /subjects/external-list
└── ➕ Create Subject
```

### For Department Heads:
```
Subjects (dropdown menu)
├── 📋 Subject List
├── 🏢 External Subjects       ← Takes you to /subjects/external-list
└── ➕ Create Subject
```

### For Admins:
```
Academic Management (dropdown menu)
├── 📋 Subjects
├── 🏢 External Subjects       ← Takes you to /subjects/external-list
├── 👥 Teams
└── ...
```

---

## Use Cases

### Use Case 1: Team Member Views Their External Subjects

**Scenario:** Alice is a member of "Team A"

**Steps:**
1. Alice logs in as a student
2. Clicks "External Subject" → "External Subjects" in menu
3. System shows all external subjects where `team_id = Team A's ID`

**Result:**
- Alice sees 2 external subjects created by her team
- Can view details of both subjects (read-only)
- Cannot edit or delete (only team leader can)

---

### Use Case 2: Team Leader Manages External Subject

**Scenario:** Bob is the leader of "Team B"

**Steps:**
1. Bob logs in and navigates to External Subjects list
2. Sees "IoT System" subject created by his team
3. Clicks Edit button → Makes changes
4. Clicks Delete button → Soft deletes the subject

**Result:**
- Bob successfully edited the subject
- Bob successfully deleted the subject (soft delete - can be restored)

---

### Use Case 3: Student Not in Team

**Scenario:** Charlie is a student but not in any team

**Steps:**
1. Charlie logs in and navigates to External Subjects list
2. System checks: Charlie has no active team
3. Shows empty state message

**Result:**
- Charlie sees a message: "You are not currently in a team"
- Encouraged to join a team

---

### Use Case 4: Admin Views All External Subjects

**Scenario:** Admin wants to see all external subjects

**Steps:**
1. Admin logs in
2. Navigates to Academic Management → External Subjects
3. System shows ALL external subjects (no filtering)

**Result:**
- Admin sees external subjects from all teams
- Can edit and delete any subject
- Can filter/search across all subjects

---

## Technical Details

### Database Query:

**For Students:**
```sql
SELECT * FROM subjects
WHERE is_external = 1
AND team_id = ?
ORDER BY created_at DESC
```

**For Admins:**
```sql
SELECT * FROM subjects
WHERE is_external = 1
ORDER BY created_at DESC
```

### Eager Loading:

To prevent N+1 queries, the following relationships are loaded:
```php
Subject::with([
    'team.members.user',        // Team and all members
    'student',                   // Student who created it
    'externalSupervisor',        // External supervisor
    'specialities',              // Associated specialities
    'projects.team'              // Related projects
])
```

---

## Authorization Logic in View

**File:** `resources/views/subjects/external-list.blade.php`
**Lines:** 151-176

### Edit Permission:
```php
$canEdit = false;

if ($user->role === 'admin') {
    $canEdit = true;
} elseif ($subject->team) {
    // Check if user is the team leader
    $userTeamMember = $subject->team->members
        ->where('student_id', $user->id)
        ->first();

    if ($userTeamMember && $userTeamMember->role === 'leader') {
        $canEdit = true;  // Only team leaders can edit
    }
}
```

### Delete Permission:
```php
$canDelete = false;

if ($user->role === 'admin') {
    $canDelete = true;
} elseif ($subject->team) {
    $userTeamMember = $subject->team->members
        ->where('student_id', $user->id)
        ->first();

    if ($userTeamMember && $userTeamMember->role === 'leader') {
        $canDelete = true;  // Only team leaders can delete
    }
}
```

---

## Benefits of Team-Based Access

### 1. **Collaboration**
- ✅ All team members can view their team's external subjects
- ✅ Team leaders have full control to edit and manage subjects
- ✅ Team members have read-only access for visibility and transparency

### 2. **Transparency**
- ✅ Clear ownership (subject belongs to team, not individual)
- ✅ All team members have equal visibility
- ✅ Easy to track team's external projects

### 3. **Control**
- ✅ Team leaders have delete permission for safety
- ✅ Other teams cannot see or access your subjects
- ✅ Admins can oversee all subjects

### 4. **Simplicity**
- ✅ Direct team_id filtering (simple SQL query)
- ✅ Easy to understand permission model
- ✅ Efficient database queries

---

## Troubleshooting

### Problem: I'm in a team but see no external subjects

**Possible Causes:**
1. Your team hasn't created any external subjects yet
2. External subjects exist but don't have `team_id` set

**Solution:**
1. Check if team has created subjects: Look in "Create External Subject"
2. Run migration command to assign team_id:
   ```bash
   php artisan subjects:assign-teams
   ```

---

### Problem: I can see but not edit external subjects

**This is expected behavior for regular team members:**
- Only team leaders can edit external subjects
- Team members have read-only access

**Solution:**
1. If you need to edit, ask your team leader to make the changes
2. Or ask your team leader to promote you to team leader role
3. Contact admin if assistance is needed

---

### Problem: Delete button not showing

**This is expected behavior:**
- Only team leaders can delete external subjects
- Regular team members can edit but not delete

**Solution:**
- If you need to delete, ask your team leader
- Or contact an admin

---

### Problem: "Attempt to read property 'name' on null" Error

**Cause:**
- Views trying to access `$subject->teacher->name` on external subjects that don't have teachers

**Fixed Locations:**
1. `resources/views/subjects/show.blade.php:329` - Subject request modal
2. `resources/views/subjects/index.blade.php:114` - Subject listing table
3. `resources/views/subjects/available.blade.php:55-56` - Available subjects page

**Solution Applied:**
- Added conditional logic to check if subject is external or internal
- Used null-safe operator `?->` on all teacher property accesses
- External subjects now display student/team info instead of teacher info

**Example Fix:**
```blade
@if($subject->is_external)
    <!-- Show student and team info -->
@else
    <p>{{ $subject->teacher?->name ?? __('app.not_assigned') }}</p>
@endif
```

---

## Summary

✅ **Team members can see all external subjects belonging to their team (read-only)**
✅ **Team leaders have full CRUD control (Create, Read, Update, Delete)**
✅ **Simple filtering by team_id ensures privacy**
✅ **Clear role-based permissions prevent unauthorized edits**
✅ **Admins have full oversight**

The `/subjects/external-list` page provides a centralized view for teams to view their external project proposals with proper access control. Team leaders have full management capabilities while team members have read-only visibility.

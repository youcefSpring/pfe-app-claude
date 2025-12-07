# External Subject View Locations

**Date:** December 2, 2025

This document lists all the views where external subjects are displayed and how they appear.

---

## 📋 Summary of Views

| View File | Purpose | External Subject Display | Filtering Applied |
|---|---|---|---|
| `subjects/external-list.blade.php` | **External subjects list table** | **Complete table with all external subject info** | **✅ Yes - role-based filtering** |
| `subjects/index.blade.php` | Main subject list | Shows creator name + "Student" label | ✅ Yes - team's own external subjects only |
| `teams/subject-preferences.blade.php` | Team preference selection | Shows "External" badge + creator + company | ✅ Yes - team's own external subjects only |
| `teams/show.blade.php` | Team details page | Shows available subjects | ✅ Yes - team's own external subjects only |
| `subjects/show.blade.php` | Subject details modal | Full subject information | N/A - single subject view |
| `subjects/modal.blade.php` | Quick view modal | Full subject information | N/A - single subject view |

---

## 1. External Subjects List Page (NEW!)

**File:** `resources/views/subjects/external-list.blade.php`
**Route:** `/subjects/external-list`
**Access:** All users (filtered by role)

### Display Format

**Table Columns:**
1. Title (with description)
2. Proposed By (student name)
3. Team (with link)
4. Company
5. External Supervisor
6. Status
7. Created Date
8. Actions (View/Edit/Delete)

### Display Example:

```
╔══════════════╦══════════════╦═════════╦══════════════╦══════════════╦══════════╦═══════════╦═══════════╗
║ Title        ║ Proposed By  ║ Team    ║ Company      ║ Ext. Super.  ║ Status   ║ Created   ║ Actions   ║
╠══════════════╬══════════════╬═════════╬══════════════╬══════════════╬══════════╬═══════════╬═══════════╣
║ ML Project   ║ Alice Smith  ║ Team A  ║ TechCorp     ║ John Doe     ║ Validated║ 01/12/2025║ 👁 ✏ 🗑    ║
║ IoT System   ║ Bob Johnson  ║ Team B  ║ IoT Inc.     ║ Jane Smith   ║ Pending  ║ 28/11/2025║ 👁        ║
╚══════════════╩══════════════╩═════════╩══════════════╩══════════════╩══════════╩═══════════╩═══════════╝
```

### Features:

**1. Role-Based Filtering:**
- **Students:** See only their team's external subjects
- **Teachers:** See external subjects they supervise
- **Department Heads:** See all external subjects
- **Admins:** See all external subjects

**2. Search & Filter:**
- Search by: Title, Company name, Student name
- Filter by: Status (Draft, Pending, Validated, Rejected)

**3. Info Alert:**
Blue information box explaining what external subjects are

**4. Actions:**
- View Details (modal)
- Edit (if owner or admin)
- Delete (if owner or admin)

**5. Empty State:**
- Shows icon and message if no external subjects found
- Button to propose first external subject (for students)

### Code Location: Lines 1-266

```blade
<!-- Table showing external subjects -->
<table class="table table-hover">
    <thead>
        <tr>
            <th>{{ __('app.title') }}</th>
            <th>{{ __('app.proposed_by') }}</th>
            <th>{{ __('app.team') }}</th>
            <th>{{ __('app.company') }}</th>
            <th>{{ __('app.external_supervisor') }}</th>
            <th>{{ __('app.status') }}</th>
            <th>{{ __('app.created') }}</th>
            <th>{{ __('app.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($externalSubjects as $subject)
            <!-- Subject row with all details -->
        @endforeach
    </tbody>
</table>
```

### Navigation Access:

The external subjects list is accessible from multiple navigation menus:

**Admin Menu:**
- Academic Management → External Subjects

**Department Head Menu:**
- Subjects → External Subjects

**Teacher Menu:**
- Subjects → External Subjects

**Student Menu:**
- External Subject → External Subjects

---

## 2. Main Subject List Page

**File:** `resources/views/subjects/index.blade.php`
**Route:** `/subjects`
**Access:** All users (filtered by role)

### Display Format

**External Subject:**
```
Title: [Subject Title]
Teacher/Creator: [Student Name]
                 (Student)
Grade: MASTER
Status: Validated
Type: 🏢 External
```

**Code Location:** Lines 107-127

```blade
<td>
    @if($subject->is_external)
        <span class="text-nowrap">
            {{ $subject->student->name ?? __('app.unknown') }}
            <small class="text-muted d-block">({{ __('app.student') }})</small>
        </span>
    @else
        <span class="text-nowrap">{{ $subject->teacher->name ?? __('app.tbd') }}</span>
    @endif
</td>
...
<td>
    @if($subject->is_external)
        <span class="badge bg-secondary">
            <i class="bi bi-building"></i> {{ __('app.external') }}
        </span>
    @else
        <span class="badge bg-primary">
            <i class="bi bi-house"></i> {{ __('app.internal') }}
        </span>
    @endif
</td>
```

### What Students See:
- ✅ All validated internal subjects (matching their speciality)
- ✅ Their own external subjects
- ✅ External subjects from their teammates
- ❌ External subjects from other teams

---

## 2. Team Subject Preferences Page

**File:** `resources/views/teams/subject-preferences.blade.php`
**Route:** `/teams/{team}/subject-preferences`
**Access:** Team members only

### Display Format

**Available Subjects List (Right Column):**
```
[Subject Title] [🏢 External]
Created By: [Student Name] (Company Name)
[Description...]
🎓 Master
```

**Code Location:** Lines 224-249

```blade
<h6 class="mb-1 subject-title">
    {{ $subject->title }}
    @if($subject->is_external)
        <span class="badge bg-warning text-dark">
            <i class="bi bi-building"></i> {{ __('app.external') }}
        </span>
    @endif
</h6>
<small class="text-muted d-block">
    <i class="fas fa-chalkboard-teacher"></i>
    @if($subject->is_external)
        {{ __('app.created_by') }}: {{ $subject->student->name ?? __('app.unknown') }}
        @if($subject->company_name)
            ({{ $subject->company_name }})
        @endif
    @else
        {{ __('app.teacher') }}: {{ $subject->teacher->name ?? __('app.not_assigned') }}
    @endif
</small>
```

### Visual Indicators:
- **Badge:** Yellow/Warning badge with building icon (🏢 External)
- **Creator:** Shows student name who created it
- **Company:** Shows company name if available
- **Icon:** Building icon for external subjects

### What Teams See:
- ✅ All validated internal subjects (matching their speciality)
- ✅ External subjects created by any team member
- ❌ External subjects from other teams

---

## 3. Team Details Page

**File:** `resources/views/teams/show.blade.php`
**Route:** `/teams/{team}`
**Access:** Team members and admins

### Display Format

Shows available subjects that the team can select (similar filtering as preferences page).

**Code Location:** Lines 202-243 (TeamController filtering)

### What's Displayed:
- List of validated subjects available for selection
- Same filtering rules as preference page

---

## 4. Subject Details Modal

**File:** `resources/views/subjects/modal.blade.php`
**Route:** `/subjects/{subject}/modal` (AJAX)
**Access:** Anyone who can see the subject

### Display Format

Full subject details including:
- Title
- Description
- Keywords
- Tools required
- Work plan
- For external subjects:
  - Student creator
  - Company name
  - External supervisor info
  - Dataset resources link

### Access Control:
- Modal can only be opened for subjects visible to the user
- Controller filtering ensures users can't access external subjects from other teams

---

## 5. Subject Show Page

**File:** `resources/views/subjects/show.blade.php`
**Route:** `/subjects/{subject}`
**Access:** Anyone who can see the subject

### Display Format

Comprehensive subject information page with:
- Full description
- Teacher/Creator information
- Team preferences (who chose this subject)
- Project assignment status

### For External Subjects Shows:
- Student creator
- Company details
- External supervisor information
- All team members who have this subject in preferences

---

## 🎨 Visual Design Elements

### Badges Used:

1. **External Subject Badge**
   ```html
   <span class="badge bg-warning text-dark">
       <i class="bi bi-building"></i> External
   </span>
   ```
   - Color: Yellow/Warning (bg-warning)
   - Icon: Building (bi-building)
   - Text: Dark (for contrast)

2. **Internal Subject Badge**
   ```html
   <span class="badge bg-primary">
       <i class="bi bi-house"></i> Internal
   </span>
   ```
   - Color: Blue/Primary
   - Icon: House
   - Text: White

3. **Student Label**
   ```html
   <small class="text-muted d-block">(Student)</small>
   ```
   - Appears under creator name
   - Indicates student-created subject

---

## 🔍 How Filtering Works

### Controller Level (Backend):
All filtering happens in controllers before data reaches views:

1. **SubjectController::index()** (Line 60-80)
   - Students see validated subjects + their team's external subjects
   - Teachers see only their subjects
   - Admins see all subjects

2. **TeamController::selectSubjectForm()** (Line 446-487)
   - Filters external subjects by team member IDs
   - Shows internal subjects to all teams

3. **TeamController::subjectPreferences()** (Line 530-571)
   - Same filtering as selectSubjectForm
   - Also excludes already-selected subjects

### SQL Query Example:
```php
$subjectsQuery->where(function($q) use ($teamMemberIds) {
    // Show all internal subjects
    $q->where(function($subq) {
        $subq->where('is_external', false)
             ->orWhereNull('is_external');
    })
    // OR show external subjects created by team members
    ->orWhere(function($subq) use ($teamMemberIds) {
        $subq->where('is_external', true)
             ->whereIn('student_id', $teamMemberIds);
    });
});
```

---

## 📊 User Experience Examples

### Example 1: Student in Team A

**Team A Members:**
- Alice (created external subject "ML Project")
- Bob (no external subjects)

**What Alice and Bob See:**
- ✅ All validated internal subjects
- ✅ "ML Project" (Alice's external subject)
- ❌ External subjects from other teams

---

### Example 2: Student in Team B

**Team B Members:**
- Charlie (created external subject "Blockchain App")
- David (created external subject "IoT System")

**What Charlie and David See:**
- ✅ All validated internal subjects
- ✅ "Blockchain App" (Charlie's)
- ✅ "IoT System" (David's)
- ❌ External subjects from other teams (like Alice's "ML Project")

---

### Example 3: Teacher

**What Teachers See:**
- ✅ Only subjects they created
- ❌ Other teachers' subjects
- ❌ External subjects from students

---

### Example 4: Admin

**What Admins See:**
- ✅ ALL subjects (internal and external)
- ✅ Full creator information for all subjects
- ✅ Can manage all subjects

---

## 🔧 Technical Implementation

### Eager Loading (Performance Optimization):

To prevent N+1 queries, we eagerly load related models:

```php
// In SubjectController
Subject::with(['teacher', 'student', 'project.team', 'teams'])
    ->withCount(['preferences as preferences_count']);

// In TeamController
$subjectsQuery->with(['teacher', 'student'])->get();
```

### Relationships Used:

**Subject Model:**
- `teacher()` - BelongsTo User (for internal subjects)
- `student()` - BelongsTo User (for external subjects)
- `externalSupervisor()` - BelongsTo User
- `teams()` - HasMany Team
- `projects()` - HasMany Project

---

## 📝 Translation Keys Used

All text is translatable in 3 languages (English, French, Arabic):

- `app.external` - "External"
- `app.internal` - "Internal"
- `app.created_by` - "Created By"
- `app.student` - "Student"
- `app.teacher` - "Teacher"
- `app.company_name` - "Company Name"
- `app.unknown` - "Unknown"
- `app.not_assigned` - "Not Assigned"

---

## ✅ Summary

**External subjects are visible in 5 main views:**
1. Subject index (main list)
2. Team subject preferences (selection interface)
3. Team details page
4. Subject modal (quick view)
5. Subject show page (full details)

**Visual indicators include:**
- Yellow "External" badge
- Creator's name (student)
- Company name
- Building icon (🏢)

**Access control ensures:**
- Teams only see their own external subjects
- Other teams cannot view or select external subjects from different teams
- Proper authorization prevents unauthorized editing/deletion

All implementations follow Laravel best practices with proper eager loading, translation support, and secure filtering at the controller level.

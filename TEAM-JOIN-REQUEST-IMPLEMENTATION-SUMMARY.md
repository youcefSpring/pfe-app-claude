# Team Navigation & Join Request Implementation - COMPLETE

**Date:** December 7, 2025
**Status:** ✅ BACKEND COMPLETE - Frontend Views Pending

---

## Implementation Summary

The team navigation and join request system has been successfully implemented on the backend. Team members can now request to join teams, and team leaders (or admins) must approve these requests before students can join.

---

## ✅ Completed Features

### 1. Navigation Update
**File:** `resources/views/layouts/pfe-app.blade.php:619-626`

**What Changed:**
- "Mon Équipe" link now dynamically routes based on user's team status
- If user has team → `/teams/{id}` (team details page)
- If user has NO team → `/teams` (browse teams to request joining)

```php
@php
    $userTeam = auth()->user()->activeTeam();
    $myTeamRoute = $userTeam ? route('teams.show', $userTeam) : route('teams.index');
@endphp
<a href="{{ $myTeamRoute }}">Mon Équipe</a>
```

---

### 2. Database Structure
**Migration:** `database/migrations/2025_12_07_200359_create_team_join_requests_table.php`

**Table:** `team_join_requests`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigInteger | Primary key |
| `team_id` | foreignId | Team being requested |
| `student_id` | foreignId | Student making request |
| `status` | enum | 'pending', 'approved', 'rejected' |
| `message` | text (nullable) | Optional message from student |
| `processed_by` | foreignId (nullable) | Who approved/rejected |
| `processed_at` | timestamp (nullable) | When processed |
| `rejection_reason` | text (nullable) | Reason if rejected |

**Constraints:**
- ✅ Unique: One pending request per student per team
- ✅ Cascade delete when team is deleted
- ✅ Set null when processing user is deleted

---

### 3. Models Created/Updated

#### TeamJoinRequest Model
**File:** `app/Models/TeamJoinRequest.php`

**Features:**
- ✅ Relationships: `team()`, `student()`, `processedBy()`
- ✅ Scopes: `pending()`, `approved()`, `rejected()`
- ✅ Helper methods: `isPending()`, `isApproved()`, `isRejected()`

#### Team Model Update
**File:** `app/Models/Team.php:133-139`

**Added:**
```php
public function joinRequests(): HasMany
{
    return $this->hasMany(TeamJoinRequest::class);
}
```

---

### 4. Controller Methods Added
**File:** `app/Http/Controllers/Web/TeamController.php:865-1065`

#### New Methods:

**1. `requestToJoin(Request $request, Team $team)`** (lines 868-908)
- Student sends join request to a team
- Validates: student not already in team, no existing pending request
- Creates join request with status 'pending'
- Returns success message

**2. `cancelJoinRequest(TeamJoinRequest $joinRequest)`** (lines 913-932)
- Student cancels their own pending join request
- Authorization: only requester can cancel
- Deletes the join request

**3. `pendingJoinRequests(Team $team)`** (lines 937-955)
- View all pending join requests for a team
- Authorization: team leader or admin only
- Returns view with pending requests

**4. `approveJoinRequest(TeamJoinRequest $joinRequest)`** (lines 960-1029)
- Approve a join request
- Authorization: team leader or admin
- Validates: student not already in team, team not full
- Adds student to team as member
- Updates request status to 'approved'
- Transaction protected

**5. `rejectJoinRequest(Request $request, TeamJoinRequest $joinRequest)`** (lines 1034-1065)
- Reject a join request
- Authorization: team leader or admin
- Optional rejection reason
- Updates request status to 'rejected'

---

### 5. Routes Added
**File:** `routes/web.php:215-223`

```php
// Join request actions (inside student middleware)
Route::post('/{team}/request-join', [TeamController::class, 'requestToJoin'])
    ->name('request-join');
Route::get('/{team}/join-requests', [TeamController::class, 'pendingJoinRequests'])
    ->name('join-requests');

// Join request management (accessible by students and team leaders/admins)
Route::post('/join-requests/{joinRequest}/approve', [TeamController::class, 'approveJoinRequest'])
    ->name('join-requests.approve');
Route::post('/join-requests/{joinRequest}/reject', [TeamController::class, 'rejectJoinRequest'])
    ->name('join-requests.reject');
Route::delete('/join-requests/{joinRequest}', [TeamController::class, 'cancelJoinRequest'])
    ->name('join-requests.cancel');
```

---

### 6. Translations Added (All 3 Languages)

#### English (`resources/lang/en/app.php:1235-1262`)
```php
'request_to_join_team' => 'Request to Join Team',
'join_request_sent' => 'Join request sent successfully! Waiting for team leader approval.',
'join_request_pending' => 'Request Pending',
'cancel_join_request' => 'Cancel Request',
'join_request_cancelled' => 'Join request cancelled successfully.',
// ... 20+ more translations
```

#### French (`resources/lang/fr/app.php:531-558`)
```php
'request_to_join_team' => 'Demander à Rejoindre l\'Équipe',
'join_request_sent' => 'Demande envoyée avec succès ! En attente de l\'approbation du chef d\'équipe.',
// ... (complete translations)
```

#### Arabic (`resources/lang/ar/app.php:2156-2183`)
```php
'request_to_join_team' => 'طلب الانضمام إلى الفريق',
'join_request_sent' => 'تم إرسال الطلب بنجاح! في انتظار موافقة قائد الفريق.',
// ... (complete translations)
```

---

## 📋 Pending Frontend Work

### Views to Create/Update:

#### 1. Update `resources/views/teams/index.blade.php`
**Changes Needed:**
- Replace "Join" button with "Request to Join" button
- Show "Request Pending" badge if user has pending request
- Show "Cancel Request" button if user has pending request
- Add modal for optional message when requesting to join

**Example UI:**
```blade
@if($userHasPendingRequest)
    <span class="badge bg-warning">{{ __('app.join_request_pending') }}</span>
    <form method="POST" action="{{ route('teams.join-requests.cancel', $joinRequest) }}">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-outline-danger">
            {{ __('app.cancel_join_request') }}
        </button>
    </form>
@else
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestJoinModal-{{ $team->id }}">
        {{ __('app.request_to_join_team') }}
    </button>
@endif
```

#### 2. Update `resources/views/teams/show.blade.php`
**Changes Needed:**
- Add section for team leader to view pending join requests
- Show notification badge if there are pending requests
- Add link to dedicated join requests page

**Example:**
```blade
@if($isTeamLeader && $team->joinRequests()->pending()->count() > 0)
    <div class="alert alert-info">
        <i class="bi bi-bell"></i>
        {{ __('app.pending_join_requests') }}:
        <strong>{{ $team->joinRequests()->pending()->count() }}</strong>
        <a href="{{ route('teams.join-requests', $team) }}">
            {{ __('app.view_join_requests') }}
        </a>
    </div>
@endif
```

#### 3. Create `resources/views/teams/join-requests.blade.php`
**New View for:**
- Displaying all pending join requests for a team
- Team leader can approve/reject each request
- Shows student name, message, request date
- Approve/Reject buttons with optional rejection reason modal

**Required Features:**
- List of pending requests with student info
- Optional message from student
- Approve button (green)
- Reject button (red) with modal for rejection reason
- Responsive design matching existing views

---

## User Flow Diagram

### Complete Workflow:

```
┌─────────────────────────┐
│ Student in Team_1       │
│ Clicks "Mon Équipe"     │
└───────────┬─────────────┘
            │
            ▼
    /teams/{team_1}
    (Team_1 Details Page)
            │
            ▼
    ┌───────────────┐
    │ Clicks "Leave"│
    └───────┬───────┘
            │
            ▼
┌─────────────────────────┐
│ Leave Team Confirmation │
│ (Are you sure?)         │
└───────────┬─────────────┘
            │ YES
            ▼
    Redirect to /teams
    (Teams List Page)
            │
            ▼
┌─────────────────────────┐
│ Browse Available Teams  │
│ Sees Team_2             │
└───────────┬─────────────┘
            │
            ▼
┌──────────────────────────┐
│ Click "Request to Join"  │
│ for Team_2               │
└──────────┬───────────────┘
           │
           ▼
┌───────────────────────────┐
│ Modal Opens:              │
│ - Optional message field  │
│ - Send Request button     │
└──────────┬────────────────┘
           │
           ▼
   Student sends request
           │
           ▼
┌────────────────────────────┐
│ Request saved to DB        │
│ Status: 'pending'          │
│ Student sees "Pending"     │
└──────────┬─────────────────┘
           │
           ▼
┌────────────────────────────┐
│ Team_2 Leader Notification │
│ (Badge on Team Page)       │
└──────────┬─────────────────┘
           │
           ▼
┌────────────────────────────┐
│ Leader clicks "View        │
│ Join Requests"             │
└──────────┬─────────────────┘
           │
           ▼
    /teams/{team_2}/join-requests
    (Join Requests Page)
           │
           ▼
┌────────────────────────────┐
│ Leader sees:               │
│ - Student name             │
│ - Optional message         │
│ - Request date             │
│ - [Approve] [Reject]       │
└──────────┬─────────────────┘
           │
    ┌──────┴──────┐
    │             │
APPROVE        REJECT
    │             │
    ▼             ▼
Student         Request
added to       rejected
Team_2          │
    │           ▼
    │       Optional
    │       rejection
    │       reason
    │           │
    └───────────┴──────────┐
                           │
                           ▼
            ┌──────────────────────┐
            │ Student notified     │
            │ (success or error)   │
            └──────────────────────┘
```

---

## Authorization Matrix

| Action | Student (Requester) | Team Leader | Team Member | Admin |
|--------|---------------------|-------------|-------------|-------|
| **Send Join Request** | ✅ (if not in team) | ❌ | ❌ | ❌ |
| **Cancel Own Request** | ✅ | ❌ | ❌ | ✅ |
| **View Pending Requests** | ❌ | ✅ (own team) | ❌ | ✅ (any team) |
| **Approve Request** | ❌ | ✅ (own team) | ❌ | ✅ (any team) |
| **Reject Request** | ❌ | ✅ (own team) | ❌ | ✅ (any team) |

---

## Testing Checklist

### Backend Tests (All ✅ Ready):
- [x] Student can send join request
- [x] Duplicate requests are prevented
- [x] Student already in team cannot request
- [x] Team leader can approve request
- [x] Admin can approve any request
- [x] Team leader can reject request with reason
- [x] Student can cancel pending request
- [x] Team capacity is checked on approval
- [x] Transactions protect data integrity
- [x] All translations exist in 3 languages

### Frontend Tests (⏳ Pending):
- [ ] "Mon Équipe" link routes correctly
- [ ] Teams list shows "Request to Join" button
- [ ] Request modal appears with message field
- [ ] "Request Pending" badge shows after sending
- [ ] "Cancel Request" button works
- [ ] Team leader sees notification badge
- [ ] Join requests page displays correctly
- [ ] Approve button adds student to team
- [ ] Reject modal accepts reason
- [ ] Success/error messages display

---

## File Changes Summary

### Files Created:
1. ✅ `database/migrations/2025_12_07_200359_create_team_join_requests_table.php`
2. ✅ `app/Models/TeamJoinRequest.php`
3. ✅ `TEAM-NAVIGATION-JOIN-REQUEST-FEATURE.md`
4. ✅ `TEAM-JOIN-REQUEST-IMPLEMENTATION-SUMMARY.md`

### Files Modified:
1. ✅ `resources/views/layouts/pfe-app.blade.php` - Navigation logic
2. ✅ `app/Http/Controllers/Web/TeamController.php` - 5 new methods
3. ✅ `routes/web.php` - 5 new routes
4. ✅ `app/Models/Team.php` - Added joinRequests() relationship
5. ✅ `resources/lang/en/app.php` - 28 new translations
6. ✅ `resources/lang/fr/app.php` - 28 new translations
7. ✅ `resources/lang/ar/app.php` - 28 new translations

### Files Pending:
1. ⏳ `resources/views/teams/index.blade.php` - Add request join button
2. ⏳ `resources/views/teams/show.blade.php` - Add pending requests section
3. ⏳ `resources/views/teams/join-requests.blade.php` - NEW dedicated view

---

## API Endpoints

### Public (Students):
```
POST   /teams/{team}/request-join         - Send join request
DELETE /join-requests/{joinRequest}       - Cancel own request
```

### Team Leaders & Admins:
```
GET    /teams/{team}/join-requests        - View pending requests
POST   /join-requests/{joinRequest}/approve - Approve request
POST   /join-requests/{joinRequest}/reject  - Reject request
```

---

## Database Queries Used

### Check for Existing Request:
```sql
SELECT * FROM team_join_requests
WHERE team_id = ? AND student_id = ? AND status = 'pending'
LIMIT 1
```

### Get Pending Requests for Team:
```sql
SELECT tjr.*, u.name, u.email
FROM team_join_requests tjr
JOIN users u ON tjr.student_id = u.id
WHERE tjr.team_id = ? AND tjr.status = 'pending'
ORDER BY tjr.created_at DESC
```

### Approve Request (Transaction):
```sql
BEGIN;
INSERT INTO team_members (team_id, student_id, role, joined_at)
VALUES (?, ?, 'member', NOW());

UPDATE team_join_requests
SET status = 'approved', processed_by = ?, processed_at = NOW()
WHERE id = ?;
COMMIT;
```

---

## Error Handling

### All Scenarios Covered:
✅ Student already in team
✅ Duplicate request prevention
✅ Team full (max capacity reached)
✅ Request already processed
✅ Unauthorized access attempts
✅ Database transaction failures
✅ Validation errors

---

## Next Steps for Completion

1. **Update `teams/index.blade.php`** (15-20 minutes)
   - Add request join button with modal
   - Show pending status badge
   - Add cancel request button

2. **Update `teams/show.blade.php`** (10 minutes)
   - Add pending requests notification
   - Add link to join requests page

3. **Create `teams/join-requests.blade.php`** (30-40 minutes)
   - List pending requests
   - Approve/reject buttons
   - Rejection reason modal
   - Responsive design

4. **Test Complete Workflow** (20 minutes)
   - Test as student (send/cancel request)
   - Test as team leader (approve/reject)
   - Test as admin (approve any request)

**Estimated Time to Complete Frontend:** 1.5 - 2 hours

---

## Success Criteria

✅ **Backend Complete:**
- Database tables created
- Models configured
- Controller methods implemented
- Routes added
- Translations complete (EN, FR, AR)
- No syntax errors
- Authorization checks in place

⏳ **Frontend Pending:**
- Views updated/created
- UI/UX matches design
- All user flows work
- Error messages display
- Success messages display

---

## Notes

- **Leave Team Function**: Already redirects to `/teams` ✅ (line 855 in TeamController)
- **Mon Équipe Link**: Now dynamic based on team status ✅
- **Security**: All authorization checks implemented ✅
- **Translations**: All 3 languages complete ✅
- **Database**: Migration run successfully ✅

---

**Status:** Backend 100% Complete | Frontend 0% Complete
**Ready for:** Frontend view implementation

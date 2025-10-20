<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\TeamSubjectPreference;
use App\Models\SubjectRequest;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'status',
        'subject_id',
        'supervisor_id',
        'external_supervisor',
        'academic_year',
    ];

    // Relationships
    /**
     * Get the subject selected by this team.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the supervisor assigned to this team.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the academic year this team belongs to.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year', 'year');
    }

    /**
     * Get the team members.
     */
    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get the students in this team.
     */
    public function students()
    {
        return $this->hasManyThrough(User::class, TeamMember::class, 'team_id', 'id', 'id', 'student_id');
    }

    /**
     * Get the team leader.
     */
    public function leader()
    {
        return $this->members()->where('role', 'leader')->first()?->student;
    }

    /**
     * Get the project associated with this team.
     */
    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    /**
     * Get the external project submitted by this team.
     */
    public function externalProject(): HasOne
    {
        return $this->hasOne(ExternalProject::class);
    }

    /**
     * Get the conflicts this team is involved in.
     */
    public function conflicts(): BelongsToMany
    {
        return $this->belongsToMany(SubjectConflict::class, 'conflict_teams', 'team_id', 'conflict_id')
            ->withPivot(['priority_score', 'selection_date'])
            ->withTimestamps();
    }

    /**
     * Get the team's subject preferences.
     */
    public function subjectPreferences(): HasMany
    {
        return $this->hasMany(TeamSubjectPreference::class)->orderBy('preference_order');
    }

    /**
     * Get the team's subject requests.
     */
    public function subjectRequests(): HasMany
    {
        return $this->hasMany(SubjectRequest::class);
    }

    // Scopes
    /**
     * Scope to get active teams.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get complete teams.
     */
    public function scopeComplete($query)
    {
        return $query->where('status', 'complete');
    }

    /**
     * Scope to get teams in formation.
     */
    public function scopeForming($query)
    {
        return $query->where('status', 'forming');
    }

    /**
     * Scope to get teams with subjects selected.
     */
    public function scopeWithSubject($query)
    {
        return $query->whereNotNull('subject_id');
    }

    // Business Logic Methods
    /**
     * Check if team is complete (has required number of members).
     */
    public function isComplete(): bool
    {
        $memberCount = $this->members()->count();
        $minSize = config('team.sizes.licence.min', 2); // Default minimum
        $maxSize = config('team.sizes.licence.max', 3); // Default maximum

        return $memberCount >= $minSize && $memberCount <= $maxSize;
    }

    /**
     * Check if team can select a subject.
     */
    public function canSelectSubject(): bool
    {
        // Auto-update status if team is complete but still forming
        if ($this->isComplete() && $this->status === 'forming') {
            $this->update(['status' => 'complete']);
            $this->refresh();
        }

        // Allow teams that are complete in size and don't have a subject
        return $this->isComplete() &&
               in_array($this->status, ['forming', 'complete']) &&
               !$this->subject_id;
    }

    /**
     * Add a member to the team.
     */
    public function addMember(User $student, string $role = 'member'): bool
    {
        // Check if student is already in another active team
        $existingMembership = TeamMember::whereHas('team', function ($query) {
            $query->whereIn('status', ['forming', 'complete', 'subject_selected', 'assigned', 'active']);
        })->where('student_id', $student->id)->exists();

        if ($existingMembership) {
            return false;
        }

        // Check team size limits
        $currentSize = $this->members()->count();
        $maxSize = config('team.sizes.licence.max', 3);

        if ($currentSize >= $maxSize) {
            return false;
        }

        // Add member
        $this->members()->create([
            'student_id' => $student->id,
            'role' => $role,
        ]);

        // Update team status if complete
        if ($this->isComplete() && $this->status === 'forming') {
            $this->update(['status' => 'complete']);
        }

        return true;
    }

    /**
     * Remove a member from the team.
     */
    public function removeMember(User $student): bool
    {
        $member = $this->members()->where('student_id', $student->id)->first();

        if (!$member) {
            return false;
        }

        // Cannot remove if team has already selected a subject
        if ($this->subject_id) {
            return false;
        }

        $member->delete();

        // Update team status if no longer complete
        if (!$this->isComplete() && $this->status === 'complete') {
            $this->update(['status' => 'forming']);
        }

        return true;
    }

    /**
     * Select a subject for this team.
     */
    public function selectSubject(Subject $subject): bool
    {
        if (!$this->canSelectSubject()) {
            return false;
        }

        if (!$subject->canBeSelected()) {
            return false;
        }

        $this->update([
            'subject_id' => $subject->id,
            'status' => 'subject_selected',
        ]);

        // Check for conflicts
        $conflict = $subject->createConflictIfNeeded($this);

        return true;
    }

    /**
     * Check if team has a member.
     */
    public function hasMember(User $student): bool
    {
        return $this->members()->where('student_id', $student->id)->exists();
    }

    /**
     * Get team's average academic score for conflict resolution.
     */
    public function getAverageScore(): float
    {
        // This would be implemented based on your academic scoring system
        // For now, return a placeholder
        return 0.0;
    }

    /**
     * Calculate priority score for conflict resolution.
     */
    public function calculatePriorityScore(): int
    {
        $score = 0;

        // Selection order (first come, first served gets higher score)
        $selectionOrder = $this->conflicts()->first()?->pivot?->selection_date ?? now();
        $score += (now()->diffInMinutes($selectionOrder)) * -1; // Earlier = higher score

        // Academic merit (if available)
        $score += $this->getAverageScore() * 10;

        // Random factor to break ties
        $score += rand(1, 100);

        return $score;
    }

    /**
     * Submit external project.
     */
    public function submitExternalProject(array $projectData): ExternalProject
    {
        return $this->externalProject()->create($projectData);
    }

    /**
     * Get team size configuration based on level.
     */
    public function getSizeConfig(): array
    {
        // This would be determined by the students' level (licence/master)
        // For now, return default
        return config('team.sizes.licence', ['min' => 2, 'max' => 3]);
    }

    /**
     * Get the best student's average marks in this team.
     * Used for subject allocation priority.
     */
    public function getAverageMarksAttribute(): float
    {
        $members = $this->members()->get();

        if ($members->isEmpty()) {
            return 0;
        }

        $bestStudentAverage = 0;

        foreach ($members as $member) {
            $userAverage = $member->user->average_percentage;
            if ($userAverage > $bestStudentAverage) {
                $bestStudentAverage = $userAverage;
            }
        }

        return round($bestStudentAverage, 2);
    }

    /**
     * Get the best performing student in this team.
     */
    public function getBestStudent(): ?User
    {
        $members = $this->members()->get();

        if ($members->isEmpty()) {
            return null;
        }

        $bestStudent = null;
        $bestAverage = 0;

        foreach ($members as $member) {
            $userAverage = $member->user->average_percentage;
            if ($userAverage > $bestAverage) {
                $bestAverage = $userAverage;
                $bestStudent = $member->user;
            }
        }

        return $bestStudent;
    }

    /**
     * Get team priority score for subject allocation.
     * Higher score = higher priority
     */
    public function getPriorityScoreAttribute(): float
    {
        // Base score is the team average marks
        $baseScore = $this->average_marks;

        // Additional factors can be added here:
        // - Team completion bonus (if team is complete)
        // - Early selection bonus (if selected subject early)
        // - Other academic factors

        $completionBonus = $this->status === 'complete' ? 5 : 0;

        return round($baseScore + $completionBonus, 2);
    }

    /**
     * Check if this team has selected a specific subject.
     */
    public function hasSelectedSubject(int $subjectId): bool
    {
        return $this->subject_id == $subjectId;
    }

    /**
     * Get teams that have selected the same subject (conflict).
     */
    public function getConflictingTeams(): \Illuminate\Database\Eloquent\Collection
    {
        if (!$this->subject_id) {
            return collect();
        }

        return Team::where('subject_id', $this->subject_id)
            ->where('id', '!=', $this->id)
            ->with(['members.user'])
            ->get();
    }

    /**
     * Add a subject to team preferences.
     */
    public function addSubjectPreference(Subject $subject, int $order, User $addedBy): bool
    {
        // Check if team already has 10 preferences
        if ($this->subjectPreferences()->count() >= TeamSubjectPreference::MAX_PREFERENCES) {
            return false;
        }

        // Check if order is valid (1-10)
        if ($order < 1 || $order > TeamSubjectPreference::MAX_PREFERENCES) {
            return false;
        }

        // Check if subject is already in preferences
        if ($this->subjectPreferences()->where('subject_id', $subject->id)->exists()) {
            return false;
        }

        // Check if order position is already taken
        if ($this->subjectPreferences()->where('preference_order', $order)->exists()) {
            // Shift other preferences down
            $this->subjectPreferences()
                ->where('preference_order', '>=', $order)
                ->increment('preference_order');
        }

        // Create the preference
        $this->subjectPreferences()->create([
            'subject_id' => $subject->id,
            'preference_order' => $order,
            'selected_at' => now(),
            'selected_by' => $addedBy->id,
        ]);

        return true;
    }

    /**
     * Remove a subject from team preferences.
     */
    public function removeSubjectPreference(Subject $subject): bool
    {
        $preference = $this->subjectPreferences()->where('subject_id', $subject->id)->first();

        if (!$preference) {
            return false;
        }

        // Don't allow removal if already allocated
        if ($preference->is_allocated) {
            return false;
        }

        $order = $preference->preference_order;
        $preference->delete();

        // Reorder remaining preferences
        $this->subjectPreferences()
            ->where('preference_order', '>', $order)
            ->decrement('preference_order');

        return true;
    }

    /**
     * Update preference order for subjects.
     */
    public function updatePreferenceOrder(array $subjectIds): bool
    {
        // Validate count doesn't exceed max
        if (count($subjectIds) > TeamSubjectPreference::MAX_PREFERENCES) {
            return false;
        }

        DB::beginTransaction();
        try {
            foreach ($subjectIds as $order => $subjectId) {
                $this->subjectPreferences()
                    ->where('subject_id', $subjectId)
                    ->update(['preference_order' => $order + 1]);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Get allocated subject from preferences.
     */
    public function getAllocatedSubject(): ?Subject
    {
        $allocated = $this->subjectPreferences()->allocated()->first();
        return $allocated ? $allocated->subject : null;
    }

    /**
     * Check if team can manage preferences.
     */
    public function canManagePreferences(): bool
    {
        // Team must be complete
        if (!$this->isComplete()) {
            return false;
        }

        // Check if allocation deadline has not passed
        $deadline = AllocationDeadline::active()->first();
        if (!$deadline || !$deadline->canStudentsChoose()) {
            return false;
        }

        // Check if no subject has been allocated yet
        if ($this->subjectPreferences()->allocated()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Prevent team deletion if it has members or subjects.
     */
    public function canBeDeleted(): bool
    {
        // Teams with more than one member cannot be deleted
        if ($this->members()->count() > 1) {
            return false;
        }

        // Teams with allocated subjects cannot be deleted
        if ($this->subjectPreferences()->allocated()->exists()) {
            return false;
        }

        // Teams with projects cannot be deleted
        if ($this->project()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Boot method to automatically set academic year for new records.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (empty($team->academic_year)) {
                $currentYear = AcademicYear::getCurrentYear();
                if ($currentYear) {
                    $team->academic_year = $currentYear->year;
                }
            }
        });
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        return $this->isComplete() && $this->status === 'complete' && !$this->subject_id;
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
}
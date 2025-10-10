<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'keywords',
        'tools',
        'plan',
        'teacher_id',
        'status',
        'validation_feedback',
        'validated_at',
        'validated_by',
        'is_external',
        'company_name',
        'dataset_resources_link',
        'student_id',
        'external_supervisor_id',
        'academic_year',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'validated_at' => 'datetime',
            'is_external' => 'boolean',
        ];
    }

    // Relationships
    /**
     * Get the teacher who created this subject.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the user who validated this subject.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Get the student who created this external subject.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the external supervisor for this subject.
     */
    public function externalSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'external_supervisor_id');
    }

    /**
     * Get the teams that selected this subject.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get the projects associated with this subject.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'subject_id');
    }

    /**
     * Get the project associated with this subject (single).
     */
    public function project(): HasOne
    {
        return $this->hasOne(Project::class, 'subject_id');
    }

    /**
     * Get the conflicts related to this subject.
     */
    public function conflicts(): HasMany
    {
        return $this->hasMany(SubjectConflict::class);
    }

    /**
     * Get the student preferences for this subject.
     */
    public function preferences(): HasMany
    {
        return $this->hasMany(SubjectPreference::class);
    }

    /**
     * Get the academic year this subject belongs to.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year', 'year');
    }

    /**
     * Get the allocations for this subject.
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(SubjectAllocation::class);
    }

    /**
     * Get the specialities that this subject is available for.
     */
    public function specialities(): BelongsToMany
    {
        return $this->belongsToMany(Speciality::class, 'subject_specialities');
    }

    // Scopes
    /**
     * Scope to get validated subjects.
     */
    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    /**
     * Scope to get pending validation subjects.
     */
    public function scopePendingValidation($query)
    {
        return $query->where('status', 'pending_validation');
    }

    /**
     * Scope to get available subjects (validated and not assigned).
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'validated')
            ->whereDoesntHave('teams', function ($query) {
                $query->where('status', 'assigned');
            });
    }

    /**
     * Scope to filter by teacher.
     */
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope to get external subjects.
     */
    public function scopeExternal($query)
    {
        return $query->where('is_external', true);
    }

    /**
     * Scope to get internal subjects.
     */
    public function scopeInternal($query)
    {
        return $query->where('is_external', false);
    }

    /**
     * Scope to filter by student (for external subjects).
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to filter subjects by speciality.
     */
    public function scopeForSpeciality($query, $specialityId)
    {
        return $query->whereHas('specialities', function ($q) use ($specialityId) {
            $q->where('specialities.id', $specialityId);
        });
    }

    /**
     * Scope to filter subjects by multiple specialities.
     */
    public function scopeForSpecialities($query, array $specialityIds)
    {
        return $query->whereHas('specialities', function ($q) use ($specialityIds) {
            $q->whereIn('specialities.id', $specialityIds);
        });
    }

    /**
     * Boot method to automatically set academic year for new records.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subject) {
            if (empty($subject->academic_year)) {
                $currentYear = AcademicYear::getCurrentYear();
                if ($currentYear) {
                    $subject->academic_year = $currentYear->year;
                }
            }
        });
    }

    // Business Logic Methods
    /**
     * Check if subject can be selected by teams.
     */
    public function canBeSelected(): bool
    {
        return $this->status === 'validated' && !$this->isAssigned();
    }

    /**
     * Check if subject is assigned to a team.
     */
    public function isAssigned(): bool
    {
        return $this->teams()->where('status', 'assigned')->exists();
    }

    /**
     * Check if subject has conflicts.
     */
    public function hasConflicts(): bool
    {
        return $this->conflicts()->where('status', 'pending')->exists();
    }

    /**
     * Validate the subject.
     */
    public function validate(User $validator, ?string $feedback = null): bool
    {
        $this->update([
            'status' => 'validated',
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'validation_feedback' => $feedback,
        ]);

        return true;
    }

    /**
     * Reject the subject.
     */
    public function reject(User $validator, string $feedback): bool
    {
        $this->update([
            'status' => 'rejected',
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'validation_feedback' => $feedback,
        ]);

        return true;
    }

    /**
     * Request corrections for the subject.
     */
    public function requestCorrections(User $validator, string $feedback): bool
    {
        $this->update([
            'status' => 'needs_correction',
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'validation_feedback' => $feedback,
        ]);

        return true;
    }

    /**
     * Create conflict if multiple teams select this subject.
     */
    public function createConflictIfNeeded(Team $team): ?SubjectConflict
    {
        // Check if another team already selected this subject
        $existingSelection = $this->teams()
            ->where('status', 'subject_selected')
            ->where('id', '!=', $team->id)
            ->exists();

        if ($existingSelection) {
            // Create or get existing conflict
            $conflict = $this->conflicts()->firstOrCreate([
                'subject_id' => $this->id,
                'status' => 'pending',
            ]);

            // Add teams to conflict
            $allTeams = $this->teams()->where('status', 'subject_selected')->get();
            foreach ($allTeams as $conflictTeam) {
                $conflict->teams()->firstOrCreate([
                    'team_id' => $conflictTeam->id,
                    'selection_date' => $conflictTeam->updated_at,
                ]);
            }

            return $conflict;
        }

        return null;
    }

    /**
     * Assign subject to a team after conflict resolution.
     */
    public function assignToTeam(Team $team): bool
    {
        // Remove other teams
        $this->teams()->where('id', '!=', $team->id)->update(['subject_id' => null]);

        // Update winning team status
        $team->update(['status' => 'assigned']);

        return true;
    }

    /**
     * Get teams competing for this subject.
     */
    public function getCompetingTeams(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->teams()
            ->where('status', 'subject_selected')
            ->with(['members.user'])
            ->get();
    }

    /**
     * Check if subject has multiple teams competing.
     */
    public function hasMultipleTeams(): bool
    {
        return $this->teams()
            ->where('status', 'subject_selected')
            ->count() > 1;
    }

    /**
     * Resolve subject conflict using team priority scoring.
     */
    public function resolveConflict(): array
    {
        $competingTeams = $this->getCompetingTeams();

        if ($competingTeams->count() <= 1) {
            return ['success' => false, 'message' => 'No conflict to resolve'];
        }

        // Calculate priority scores for all teams
        $teamScores = $competingTeams->map(function ($team) {
            return [
                'team' => $team,
                'priority_score' => $team->priority_score,
                'average_marks' => $team->average_marks
            ];
        });

        // Sort by priority score (highest first)
        $rankedTeams = $teamScores->sortByDesc('priority_score');

        $winner = $rankedTeams->first();
        $losers = $rankedTeams->slice(1);

        // Assign subject to winning team
        $this->assignSubjectToWinner($winner['team']);

        // Reset other teams
        foreach ($losers as $loser) {
            $this->resetTeamSelection($loser['team']);
        }

        // Mark any existing conflicts as resolved
        $this->markConflictsResolved($winner['team']);

        return [
            'success' => true,
            'winner' => $winner,
            'losers' => $losers->toArray(),
            'total_teams' => $competingTeams->count()
        ];
    }

    /**
     * Assign subject to the winning team.
     */
    private function assignSubjectToWinner(Team $team): void
    {
        $team->update([
            'subject_id' => $this->id,
            'status' => 'assigned'
        ]);

        $this->update([
            'status' => 'assigned'
        ]);
    }

    /**
     * Reset team's subject selection.
     */
    private function resetTeamSelection(Team $team): void
    {
        $team->update([
            'subject_id' => null,
            'status' => 'complete'
        ]);
    }

    /**
     * Mark conflicts as resolved.
     */
    private function markConflictsResolved(Team $winningTeam): void
    {
        $this->conflicts()
            ->where('status', 'pending')
            ->update([
                'status' => 'resolved',
                'winning_team_id' => $winningTeam->id,
                'resolved_at' => now()
            ]);
    }

    /**
     * Get the status badge color for display.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'draft' => 'status-draft',
            'pending_validation' => 'status-pending',
            'validated' => 'status-validated',
            'rejected' => 'status-rejected',
            'needs_correction' => 'status-pending',
            'assigned' => 'status-assigned',
            default => 'status-draft'
        };
    }

    /**
     * Check if subject selection is currently in conflict.
     */
    public function isInConflict(): bool
    {
        return $this->hasMultipleTeams() || $this->hasConflicts();
    }

    /**
     * Get conflict priority information.
     */
    public function getConflictInfo(): array
    {
        if (!$this->isInConflict()) {
            return ['has_conflict' => false];
        }

        $competingTeams = $this->getCompetingTeams();

        return [
            'has_conflict' => true,
            'team_count' => $competingTeams->count(),
            'teams' => $competingTeams->map(function ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'priority_score' => $team->priority_score,
                    'average_marks' => $team->average_marks,
                    'member_count' => $team->members()->count()
                ];
            })->toArray()
        ];
    }
}
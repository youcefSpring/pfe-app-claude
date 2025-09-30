<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    public function validate(User $validator, string $feedback = null): bool
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
}
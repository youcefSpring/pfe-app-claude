<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'subject_id',
        'external_project_id',
        'supervisor_id',
        'co_supervisor_id',
        'type',
        'status',
        'started_at',
        'submitted_at',
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
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    // Relationships
    /**
     * Get the team working on this project.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the subject for internal projects.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the external project details.
     */
    public function externalProject(): BelongsTo
    {
        return $this->belongsTo(ExternalProject::class);
    }

    /**
     * Get the main supervisor.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the co-supervisor for external projects.
     */
    public function coSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_supervisor_id');
    }

    /**
     * Get the academic year this project belongs to.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year', 'year');
    }

    /**
     * Get the submissions for this project.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get the defense for this project.
     */
    public function defense(): HasOne
    {
        return $this->hasOne(Defense::class);
    }

    // Scopes
    /**
     * Scope to get internal projects.
     */
    public function scopeInternal($query)
    {
        return $query->where('type', 'internal');
    }

    /**
     * Scope to get external projects.
     */
    public function scopeExternal($query)
    {
        return $query->where('type', 'external');
    }

    /**
     * Scope to get active projects.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['assigned', 'in_progress']);
    }

    /**
     * Scope to get projects by supervisor.
     */
    public function scopeBySupervisor($query, $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    /**
     * Scope to get projects ready for defense.
     */
    public function scopeReadyForDefense($query)
    {
        return $query->where('status', 'submitted')
            ->whereHas('submissions', function ($query) {
                $query->where('type', 'final_report')
                    ->where('status', 'approved');
            });
    }

    // Business Logic Methods
    /**
     * Check if project is internal.
     */
    public function isInternal(): bool
    {
        return $this->type === 'internal';
    }

    /**
     * Check if project is external.
     */
    public function isExternal(): bool
    {
        return $this->type === 'external';
    }

    /**
     * Check if project is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['assigned', 'in_progress']);
    }

    /**
     * Check if project can submit deliverables.
     */
    public function canSubmitDeliverable(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if project is ready for defense.
     */
    public function isReadyForDefense(): bool
    {
        if ($this->status !== 'submitted') {
            return false;
        }

        return $this->submissions()
            ->where('type', 'final_report')
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Start the project.
     */
    public function start(): bool
    {
        if ($this->status !== 'assigned') {
            return false;
        }

        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return true;
    }

    /**
     * Submit the project for defense.
     */
    public function submit(): bool
    {
        if ($this->status !== 'in_progress') {
            return false;
        }

        // Check if final report is approved
        $finalReportApproved = $this->submissions()
            ->where('type', 'final_report')
            ->where('status', 'approved')
            ->exists();

        if (!$finalReportApproved) {
            return false;
        }

        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark project as defended.
     */
    public function markAsDefended(): bool
    {
        if ($this->status !== 'submitted') {
            return false;
        }

        $this->update(['status' => 'defended']);

        return true;
    }

    /**
     * Assign supervisor to the project.
     */
    public function assignSupervisor(User $supervisor, ?User $coSupervisor = null): bool
    {
        if (!$supervisor->isTeacher() && !$supervisor->isExternalSupervisor()) {
            return false;
        }

        $this->update([
            'supervisor_id' => $supervisor->id,
            'co_supervisor_id' => $coSupervisor?->id,
        ]);

        return true;
    }

    /**
     * Get project title.
     */
    public function getTitle(): string
    {
        if ($this->isInternal()) {
            return $this->subject?->title ?? 'Untitled Internal Project';
        }

        return $this->externalProject?->project_description ?? 'Untitled External Project';
    }

    /**
     * Get project description.
     */
    public function getDescription(): string
    {
        if ($this->isInternal()) {
            return $this->subject?->description ?? '';
        }

        return $this->externalProject?->project_description ?? '';
    }

    /**
     * Get all supervisors (main + co-supervisor).
     */
    public function getAllSupervisors(): array
    {
        $supervisors = [];

        if ($this->supervisor) {
            $supervisors[] = $this->supervisor;
        }

        if ($this->coSupervisor) {
            $supervisors[] = $this->coSupervisor;
        }

        return $supervisors;
    }

    /**
     * Get latest submission of a specific type.
     */
    public function getLatestSubmission(string $type): ?Submission
    {
        return $this->submissions()
            ->where('type', $type)
            ->latest()
            ->first();
    }

    /**
     * Get project progress percentage.
     */
    public function getProgressPercentage(): int
    {
        return match ($this->status) {
            'assigned' => 10,
            'in_progress' => 50,
            'submitted' => 90,
            'defended' => 100,
            default => 0,
        };
    }

    /**
     * Boot method to automatically set academic year for new records.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->academic_year)) {
                $currentYear = AcademicYear::getCurrentYear();
                if ($currentYear) {
                    $project->academic_year = $currentYear->year;
                }
            }
        });
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AllocationDeadline extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'academic_year',
        'level',
        'preferences_start',
        'preferences_deadline',
        'grades_verification_deadline',
        'allocation_date',
        'second_round_start',
        'second_round_deadline',
        'defense_scheduling_allowed_after',
        'status',
        'description',
        'created_by',
        'auto_allocation_completed',
        'second_round_needed',
    ];

    protected function casts(): array
    {
        return [
            'preferences_start' => 'datetime',
            'preferences_deadline' => 'datetime',
            'grades_verification_deadline' => 'datetime',
            'allocation_date' => 'datetime',
            'second_round_start' => 'datetime',
            'second_round_deadline' => 'datetime',
            'defense_scheduling_allowed_after' => 'datetime',
            'auto_allocation_completed' => 'boolean',
            'second_round_needed' => 'boolean',
        ];
    }

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(SubjectAllocation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    // Business Logic
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function canStudentsChoose(): bool
    {
        return $this->isActive() &&
               now()->between($this->preferences_start, $this->preferences_deadline);
    }

    public function isPreferencesDeadlinePassed(): bool
    {
        return now()->gt($this->preferences_deadline);
    }

    public function isGradesVerificationDeadlinePassed(): bool
    {
        return now()->gt($this->grades_verification_deadline);
    }

    public function canAllocate(): bool
    {
        return $this->isPreferencesDeadlinePassed() &&
               $this->isGradesVerificationDeadlinePassed() &&
               now()->gte($this->allocation_date);
    }

    public function getRemainingTimeForPreferences(): string
    {
        if (!$this->canStudentsChoose()) {
            return 'Deadline passed';
        }

        return $this->preferences_deadline->diffForHumans();
    }

    public function updateStatus(): void
    {
        if ($this->canAllocate()) {
            $this->update(['status' => 'completed']);
        } elseif ($this->isGradesVerificationDeadlinePassed()) {
            $this->update(['status' => 'grades_pending']);
        } elseif ($this->isPreferencesDeadlinePassed()) {
            $this->update(['status' => 'preferences_closed']);
        }
    }

    /**
     * Check if defense scheduling is allowed (deadline passed)
     */
    public function canScheduleDefenses(): bool
    {
        return $this->defense_scheduling_allowed_after
            ? now()->gte($this->defense_scheduling_allowed_after)
            : $this->isPreferencesDeadlinePassed();
    }

    /**
     * Check if second round is active
     */
    public function isSecondRoundActive(): bool
    {
        return $this->second_round_needed &&
               $this->second_round_start &&
               $this->second_round_deadline &&
               now()->between($this->second_round_start, $this->second_round_deadline);
    }

    /**
     * Check if second round deadline has passed
     */
    public function isSecondRoundDeadlinePassed(): bool
    {
        return $this->second_round_deadline && now()->gt($this->second_round_deadline);
    }

    /**
     * Check if auto-allocation can be performed
     */
    public function canPerformAutoAllocation(): bool
    {
        return $this->isPreferencesDeadlinePassed() &&
               $this->isGradesVerificationDeadlinePassed() &&
               !$this->auto_allocation_completed;
    }

    /**
     * Mark auto-allocation as completed
     */
    public function markAutoAllocationCompleted(): void
    {
        $this->update([
            'auto_allocation_completed' => true,
            'status' => 'auto_allocation_completed'
        ]);
    }

    /**
     * Initialize second round for teams without subjects
     */
    public function initializeSecondRound(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate): void
    {
        $this->update([
            'second_round_needed' => true,
            'second_round_start' => $startDate,
            'second_round_deadline' => $endDate,
            'status' => 'second_round_active'
        ]);
    }
}

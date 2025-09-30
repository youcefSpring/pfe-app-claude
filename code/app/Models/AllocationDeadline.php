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
        'status',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'preferences_start' => 'datetime',
            'preferences_deadline' => 'datetime',
            'grades_verification_deadline' => 'datetime',
            'allocation_date' => 'datetime',
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
}

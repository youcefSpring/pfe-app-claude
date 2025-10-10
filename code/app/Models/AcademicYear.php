<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'title',
        'start_date',
        'end_date',
        'status',
        'is_current',
        'description',
        'statistics',
        'ended_at',
        'ended_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'ended_at' => 'datetime',
            'is_current' => 'boolean',
            'statistics' => 'array',
        ];
    }

    // Relationships
    public function endedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ended_by');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'academic_year', 'year');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'academic_year', 'year');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'academic_year', 'year');
    }

    public function defenses(): HasMany
    {
        return $this->hasMany(Defense::class, 'academic_year', 'year');
    }

    public function allocationDeadlines(): HasMany
    {
        return $this->hasMany(AllocationDeadline::class, 'academic_year', 'year');
    }

    // Scopes
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Static methods
    public static function getCurrentYear(): ?self
    {
        return static::current()->first();
    }

    public static function getCurrentYearString(): string
    {
        $current = static::getCurrentYear();
        return $current ? $current->year : date('Y') . '-' . (date('Y') + 1);
    }

    // Instance methods
    public function isCurrent(): bool
    {
        return $this->is_current;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeEnded(): bool
    {
        return $this->isActive() && $this->isCurrent();
    }

    public function canBeEdited(): bool
    {
        return $this->isCurrent() && !$this->isCompleted();
    }

    public function endYear(User $user): bool
    {
        if (!$this->canBeEnded()) {
            return false;
        }

        // Calculate statistics before ending
        $statistics = $this->calculateStatistics();

        $this->update([
            'status' => 'completed',
            'is_current' => false,
            'ended_at' => now(),
            'ended_by' => $user->id,
            'statistics' => $statistics,
        ]);

        return true;
    }

    public function calculateStatistics(): array
    {
        return [
            'total_subjects' => $this->subjects()->count(),
            'total_teams' => $this->teams()->count(),
            'total_projects' => $this->projects()->count(),
            'total_defenses' => $this->defenses()->count(),
            'completed_defenses' => $this->defenses()->where('status', 'completed')->count(),
            'total_students' => $this->teams()->with('members')->get()->pluck('members')->flatten()->count(),
            'total_teachers' => $this->subjects()->distinct('teacher_id')->count('teacher_id'),
            'subjects_by_type' => $this->subjects()
                ->selectRaw('keywords as type, count(*) as count')
                ->groupBy('keywords')
                ->pluck('count', 'type')
                ->toArray(),
            'defenses_by_status' => $this->defenses()
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'average_defense_grade' => $this->defenses()
                ->whereNotNull('final_grade')
                ->avg('final_grade'),
            'calculated_at' => now()->toISOString(),
        ];
    }

    public function getFormattedDateRange(): string
    {
        return $this->start_date->format('d/m/Y') . ' - ' . $this->end_date->format('d/m/Y');
    }

    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getProgressPercentage(): float
    {
        if ($this->isCompleted()) {
            return 100;
        }

        $today = Carbon::today();
        if ($today->lt($this->start_date)) {
            return 0;
        }

        if ($today->gt($this->end_date)) {
            return 100;
        }

        $totalDays = $this->getDurationInDays();
        $daysPassed = $this->start_date->diffInDays($today);

        return min(100, ($daysPassed / $totalDays) * 100);
    }
}

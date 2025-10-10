<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Defense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'subject_id',
        'room_id',
        'defense_date',
        'defense_time',
        'duration',
        'status',
        'notes',
        'pv_notes',
        'final_grade',
        'scheduled_by',
        'scheduled_at',
        'academic_year',
        'session',
    ];

    protected function casts(): array
    {
        return [
            'defense_date' => 'date',
            'defense_time' => 'datetime:H:i',
            'final_grade' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function jury(): HasMany
    {
        return $this->hasMany(DefenseJury::class);
    }

    public function juries(): HasMany
    {
        return $this->jury(); // Alias for consistency
    }

    public function report(): HasOne
    {
        return $this->hasOne(DefenseReport::class);
    }

    /**
     * Get the academic year this defense belongs to.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year', 'year');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('defense_date', '>=', now()->toDateString())
            ->where('status', 'scheduled');
    }

    public function complete(float $finalGrade, ?string $notes = null): bool
    {
        $this->update([
            'status' => 'completed',
            'final_grade' => $finalGrade,
            'notes' => $notes,
        ]);

        // Mark project as defended
        $this->project->markAsDefended();

        return true;
    }

    public function getJuryByRole(string $role): ?DefenseJury
    {
        return $this->jury()->where('role', $role)->first();
    }

    public function hasJuryMember(User $teacher): bool
    {
        return $this->jury()->where('teacher_id', $teacher->id)->exists();
    }

    /**
     * Boot method to automatically set academic year and session for new records.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($defense) {
            $currentYear = AcademicYear::getCurrentYear();

            if (empty($defense->academic_year) && $currentYear) {
                $defense->academic_year = $currentYear->year;
            }

            // Determine session based on defense date vs academic year end date
            if (empty($defense->session) && $currentYear && $defense->defense_date) {
                $defenseDate = \Carbon\Carbon::parse($defense->defense_date);
                $defense->session = $defenseDate->gt($currentYear->end_date) ? 'session_2' : 'session_1';
            }
        });
    }
}
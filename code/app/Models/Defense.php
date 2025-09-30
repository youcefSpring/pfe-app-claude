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
        'room_id',
        'defense_date',
        'defense_time',
        'duration',
        'status',
        'notes',
        'final_grade',
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

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('defense_date', '>=', now()->toDateString())
            ->where('status', 'scheduled');
    }

    public function complete(float $finalGrade, string $notes = null): bool
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
}
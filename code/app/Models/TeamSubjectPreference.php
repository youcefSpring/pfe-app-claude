<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamSubjectPreference extends Model
{
    protected $fillable = [
        'team_id',
        'subject_id',
        'preference_order',
        'selected_at',
        'selected_by',
        'is_allocated',
    ];

    protected $casts = [
        'selected_at' => 'datetime',
        'is_allocated' => 'boolean',
    ];

    const MAX_PREFERENCES = 10;

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function selectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'selected_by');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('preference_order', 'asc');
    }

    public function scopeAllocated($query)
    {
        return $query->where('is_allocated', true);
    }

    public function scopeNotAllocated($query)
    {
        return $query->where('is_allocated', false);
    }

    public function getPreferenceLabel(): string
    {
        return match($this->preference_order) {
            1 => '1st Choice',
            2 => '2nd Choice',
            3 => '3rd Choice',
            default => "{$this->preference_order}th Choice"
        };
    }
}

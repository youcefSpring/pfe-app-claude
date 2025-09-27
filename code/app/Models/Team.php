<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'leader_id',
        'size',
        'status',
        'formation_completed_at',
    ];

    protected $casts = [
        'formation_completed_at' => 'datetime',
    ];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function subjectPreferences(): HasMany
    {
        return $this->hasMany(TeamSubjectPreference::class);
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }
}
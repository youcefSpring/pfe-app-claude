<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'keywords',
        'required_tools',
        'max_teams',
        'supervisor_id',
        'external_supervisor',
        'external_company',
        'status',
        'validation_notes',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'keywords' => 'array',
        'required_tools' => 'array',
        'validated_at' => 'datetime',
    ];

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function teamPreferences(): HasMany
    {
        return $this->hasMany(TeamSubjectPreference::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
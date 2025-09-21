<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamSubjectPreference extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'team_id',
        'subject_id',
        'preference_order',
    ];

    protected $dates = [
        'created_at',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
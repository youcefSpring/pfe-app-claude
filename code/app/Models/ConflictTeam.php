<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConflictTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'conflict_id',
        'team_id',
        'priority_score',
        'selection_date',
    ];

    protected function casts(): array
    {
        return [
            'selection_date' => 'datetime',
        ];
    }

    public function conflict(): BelongsTo
    {
        return $this->belongsTo(SubjectConflict::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
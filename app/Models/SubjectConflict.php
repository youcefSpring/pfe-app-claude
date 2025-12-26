<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubjectConflict extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'status',
        'resolved_by',
        'resolved_at',
        'resolution_notes',
        'winning_team_id',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function winningTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winning_team_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'conflict_teams', 'conflict_id', 'team_id')
            ->withPivot(['priority_score', 'selection_date'])
            ->withTimestamps();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function resolve(User $resolver, Team $winningTeam, string $notes = null): bool
    {
        $this->update([
            'status' => 'resolved',
            'resolved_by' => $resolver->id,
            'resolved_at' => now(),
            'winning_team_id' => $winningTeam->id,
            'resolution_notes' => $notes,
        ]);

        // Assign subject to winning team
        $this->subject->assignToTeam($winningTeam);

        return true;
    }
}
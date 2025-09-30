<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExternalProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'company',
        'contact_person',
        'contact_email',
        'contact_phone',
        'project_description',
        'technologies',
        'team_id',
        'assigned_supervisor_id',
        'status',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function assignedSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_supervisor_id');
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    public function scopePendingAssignment($query)
    {
        return $query->where('status', 'submitted');
    }

    public function assignSupervisor(User $supervisor): bool
    {
        $this->update([
            'assigned_supervisor_id' => $supervisor->id,
            'status' => 'assigned',
        ]);

        return true;
    }
}
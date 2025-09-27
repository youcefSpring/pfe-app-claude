<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Defense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'room_id',
        'defense_date',
        'start_time',
        'end_time',
        'duration',
        'jury_president_id',
        'jury_examiner_id',
        'jury_supervisor_id',
        'status',
        'notes',
        'final_grade',
        'grade_president',
        'grade_examiner',
        'grade_supervisor',
        'observations',
        'pv_generated',
        'pv_file_path',
        'scheduled_at',
        'completed_at',
    ];

    protected $casts = [
        'defense_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'final_grade' => 'decimal:2',
        'grade_president' => 'decimal:2',
        'grade_examiner' => 'decimal:2',
        'grade_supervisor' => 'decimal:2',
        'pv_generated' => 'boolean',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(PfeProject::class, 'project_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function juryPresident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jury_president_id');
    }

    public function juryExaminer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jury_examiner_id');
    }

    public function jurySupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jury_supervisor_id');
    }
}
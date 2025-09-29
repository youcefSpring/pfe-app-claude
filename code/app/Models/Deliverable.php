<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deliverable extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'file_path',
        'file_size',
        'file_type',
        'is_final_report',
        'status',
        'submitted_by',
        'reviewed_by',
        'review_comments',
        'submitted_at',
        'reviewed_at',
        'milestone_id',
        'sprint_number',
        'version_number',
        'deadline',
        'priority',
        'acceptance_criteria',
        'feedback_summary',
        'revision_requested',
        'approved_at',
    ];

    protected $casts = [
        'is_final_report' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'deadline' => 'datetime',
        'approved_at' => 'datetime',
        'revision_requested' => 'boolean',
        'acceptance_criteria' => 'array',
        'feedback_summary' => 'array',
        'version_number' => 'decimal:1'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(PfeProject::class, 'project_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->where('status', '!=', 'approved');
    }

    public function scopeBySprint($query, int $sprintNumber)
    {
        return $query->where('sprint_number', $sprintNumber);
    }

    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== 'approved';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function needsRevision(): bool
    {
        return $this->revision_requested || $this->status === 'needs_revision';
    }
}
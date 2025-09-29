<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'expected_date',
        'completed_date',
        'status',
        'weight_percentage',
        'order_sequence',
        'requirements',
        'completion_criteria',
        'notes'
    ];

    protected $casts = [
        'expected_date' => 'date',
        'completed_date' => 'date',
        'weight_percentage' => 'decimal:2',
        'requirements' => 'array',
        'completion_criteria' => 'array'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(PfeProject::class, 'project_id');
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(Deliverable::class, 'milestone_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
                    ->where('expected_date', '<', now());
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'completed' && $this->expected_date < now();
    }

    public function getProgressPercentage(): float
    {
        $totalDeliverables = $this->deliverables()->count();
        if ($totalDeliverables === 0) {
            return $this->isCompleted() ? 100.0 : 0.0;
        }

        $completedDeliverables = $this->deliverables()
            ->whereIn('status', ['approved', 'completed'])
            ->count();

        return ($completedDeliverables / $totalDeliverables) * 100;
    }
}
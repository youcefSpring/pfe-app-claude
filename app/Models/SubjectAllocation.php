<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'allocation_deadline_id',
        'student_id',
        'subject_id',
        'student_preference_order',
        'student_average',
        'allocation_rank',
        'allocation_method',
        'allocation_notes',
        'status',
        'confirmed_by',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'student_average' => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    // Relationships
    public function allocationDeadline(): BelongsTo
    {
        return $this->belongsTo(AllocationDeadline::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeTentative($query)
    {
        return $query->where('status', 'tentative');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    // Business Logic
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isTentative(): bool
    {
        return $this->status === 'tentative';
    }

    public function wasFirstChoice(): bool
    {
        return $this->student_preference_order === 1;
    }

    public function getPreferenceLabel(): string
    {
        $order = $this->student_preference_order;
        return match($order) {
            1 => '1st Choice',
            2 => '2nd Choice',
            3 => '3rd Choice',
            default => "{$order}th Choice"
        };
    }

    public function confirm(User $confirmer, string $notes = null): bool
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_by' => $confirmer->id,
            'confirmed_at' => now(),
            'allocation_notes' => $notes,
        ]);

        return true;
    }

    public function reject(User $confirmer, string $notes = null): bool
    {
        $this->update([
            'status' => 'rejected',
            'confirmed_by' => $confirmer->id,
            'confirmed_at' => now(),
            'allocation_notes' => $notes,
        ]);

        return true;
    }
}

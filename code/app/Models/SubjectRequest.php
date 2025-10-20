<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'subject_id',
        'requested_by',
        'status',
        'priority_order',
        'request_message',
        'admin_response',
        'requested_at',
        'responded_at',
        'responded_by',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    // Relationships
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Business logic methods
    public function approve(User $admin, ?string $response = null): bool
    {
        return $this->update([
            'status' => 'approved',
            'admin_response' => $response,
            'responded_at' => now(),
            'responded_by' => $admin->id,
        ]);
    }

    public function reject(User $admin, string $response): bool
    {
        return $this->update([
            'status' => 'rejected',
            'admin_response' => $response,
            'responded_at' => now(),
            'responded_by' => $admin->id,
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-secondary'
        };
    }
}

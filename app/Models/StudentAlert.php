<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'admin_response',
        'responded_at',
        'responded_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function markAsResponded(string $response, int $adminId): void
    {
        $this->update([
            'admin_response' => $response,
            'responded_at' => now(),
            'responded_by' => $adminId,
            'status' => 'responded',
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'preference_order',
        'submitted_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByPreferenceOrder($query, $order)
    {
        return $query->where('preference_order', $order);
    }

    // Business Logic
    public function isFirstChoice(): bool
    {
        return $this->preference_order === 1;
    }

    public function getPreferenceLabel(): string
    {
        $order = $this->preference_order;
        return match($order) {
            1 => '1st Choice',
            2 => '2nd Choice',
            3 => '3rd Choice',
            default => "{$order}th Choice"
        };
    }
}

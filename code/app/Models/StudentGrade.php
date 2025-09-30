<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_name',
        'semester',
        'academic_year',
        'grade',
        'coefficient',
        'status',
        'verification_notes',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'grade' => 'decimal:2',
            'coefficient' => 'decimal:1',
            'verified_at' => 'datetime',
        ];
    }

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopePendingVerification($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    // Business Logic
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function getWeightedGrade(): float
    {
        return $this->grade * $this->coefficient;
    }

    public function verify(User $verifier, string $notes = null): bool
    {
        $this->update([
            'status' => 'verified',
            'verified_by' => $verifier->id,
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);

        return true;
    }

    public function reject(User $verifier, string $notes): bool
    {
        $this->update([
            'status' => 'rejected',
            'verified_by' => $verifier->id,
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);

        return true;
    }
}

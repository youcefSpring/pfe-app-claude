<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefenseJury extends Model
{
    use HasFactory;

    protected $fillable = [
        'defense_id',
        'teacher_id',
        'role',
        'individual_grade',
        'comments',
    ];

    protected function casts(): array
    {
        return [
            'individual_grade' => 'decimal:2',
        ];
    }

    public function defense(): BelongsTo
    {
        return $this->belongsTo(Defense::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function isPresident(): bool
    {
        return $this->role === 'president';
    }

    public function isExaminer(): bool
    {
        return $this->role === 'examiner';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function submitGrade(float $grade, string $comments = null): bool
    {
        $this->update([
            'individual_grade' => $grade,
            'comments' => $comments,
        ]);

        return true;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_name',
        'mark',
        'max_mark',
        'semester',
        'academic_year',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'mark' => 'decimal:2',
            'max_mark' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the percentage score for this mark
     */
    public function getPercentageAttribute(): float
    {
        if ($this->max_mark == 0) {
            return 0;
        }
        return round(($this->mark / $this->max_mark) * 100, 2);
    }

    /**
     * Get the letter grade based on percentage
     */
    public function getLetterGradeAttribute(): string
    {
        $percentage = $this->percentage;

        if ($percentage >= 90) return 'A+';
        if ($percentage >= 85) return 'A';
        if ($percentage >= 80) return 'A-';
        if ($percentage >= 75) return 'B+';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 65) return 'B-';
        if ($percentage >= 60) return 'C+';
        if ($percentage >= 55) return 'C';
        if ($percentage >= 50) return 'C-';
        if ($percentage >= 45) return 'D+';
        if ($percentage >= 40) return 'D';
        return 'F';
    }
}

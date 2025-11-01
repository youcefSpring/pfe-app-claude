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
        'academic_year',
        'notes',
        'created_by',
        // Multiple marks
        'mark_1', 'mark_2', 'mark_3', 'mark_4', 'mark_5',
        'max_mark_1', 'max_mark_2', 'max_mark_3', 'max_mark_4', 'max_mark_5',
        'weight_1', 'weight_2', 'weight_3', 'weight_4', 'weight_5',
    ];

    protected function casts(): array
    {
        return [
            'mark' => 'decimal:2',
            'max_mark' => 'decimal:2',
            'mark_1' => 'decimal:2',
            'mark_2' => 'decimal:2',
            'mark_3' => 'decimal:2',
            'mark_4' => 'decimal:2',
            'mark_5' => 'decimal:2',
            'max_mark_1' => 'decimal:2',
            'max_mark_2' => 'decimal:2',
            'max_mark_3' => 'decimal:2',
            'max_mark_4' => 'decimal:2',
            'max_mark_5' => 'decimal:2',
            'weight_1' => 'decimal:2',
            'weight_2' => 'decimal:2',
            'weight_3' => 'decimal:2',
            'weight_4' => 'decimal:2',
            'weight_5' => 'decimal:2',
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

    /**
     * Calculate final mark based on multiple marks and weights
     */
    public function getFinalMarkAttribute(): float
    {
        $finalMark = 0;
        $totalWeight = 0;

        for ($i = 1; $i <= 5; $i++) {
            $mark = $this->{"mark_$i"};
            $maxMark = $this->{"max_mark_$i"};
            $weight = $this->{"weight_$i"};

            if ($mark !== null && $maxMark > 0 && $weight > 0) {
                $percentage = ($mark / $maxMark) * 100;
                $finalMark += ($percentage * $weight / 100);
                $totalWeight += $weight;
            }
        }

        return $totalWeight > 0 ? round($finalMark, 2) : 0;
    }

    /**
     * Get final percentage (alias for final_mark)
     */
    public function getFinalPercentageAttribute(): float
    {
        return $this->final_mark;
    }

    /**
     * Get final letter grade based on final percentage
     */
    public function getFinalLetterGradeAttribute(): string
    {
        $percentage = $this->final_percentage;

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

    /**
     * Check if mark 1 and mark 2 are required and present
     */
    public function hasRequiredMarks(): bool
    {
        return $this->mark_1 !== null && $this->mark_2 !== null;
    }

    /**
     * Get all marks as array
     */
    public function getMarksArray(): array
    {
        return [
            1 => [
                'mark' => $this->mark_1,
                'max_mark' => $this->max_mark_1,
                'weight' => $this->weight_1,
                'percentage' => $this->mark_1 && $this->max_mark_1 ? round(($this->mark_1 / $this->max_mark_1) * 100, 2) : 0
            ],
            2 => [
                'mark' => $this->mark_2,
                'max_mark' => $this->max_mark_2,
                'weight' => $this->weight_2,
                'percentage' => $this->mark_2 && $this->max_mark_2 ? round(($this->mark_2 / $this->max_mark_2) * 100, 2) : 0
            ],
            3 => [
                'mark' => $this->mark_3,
                'max_mark' => $this->max_mark_3,
                'weight' => $this->weight_3,
                'percentage' => $this->mark_3 && $this->max_mark_3 ? round(($this->mark_3 / $this->max_mark_3) * 100, 2) : 0
            ],
            4 => [
                'mark' => $this->mark_4,
                'max_mark' => $this->max_mark_4,
                'weight' => $this->weight_4,
                'percentage' => $this->mark_4 && $this->max_mark_4 ? round(($this->mark_4 / $this->max_mark_4) * 100, 2) : 0
            ],
            5 => [
                'mark' => $this->mark_5,
                'max_mark' => $this->max_mark_5,
                'weight' => $this->weight_5,
                'percentage' => $this->mark_5 && $this->max_mark_5 ? round(($this->mark_5 / $this->max_mark_5) * 100, 2) : 0
            ],
        ];
    }
}

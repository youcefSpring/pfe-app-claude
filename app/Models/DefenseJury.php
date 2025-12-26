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
        // Defense grade components
        'presentation_grade',
        'content_grade',
        'methodology_grade',
        'innovation_grade',
        'questions_grade',
        // Grade weights
        'presentation_weight',
        'content_weight',
        'methodology_weight',
        'innovation_weight',
        'questions_weight',
    ];

    protected function casts(): array
    {
        return [
            'individual_grade' => 'decimal:2',
            'presentation_grade' => 'decimal:2',
            'content_grade' => 'decimal:2',
            'methodology_grade' => 'decimal:2',
            'innovation_grade' => 'decimal:2',
            'questions_grade' => 'decimal:2',
            'presentation_weight' => 'decimal:2',
            'content_weight' => 'decimal:2',
            'methodology_weight' => 'decimal:2',
            'innovation_weight' => 'decimal:2',
            'questions_weight' => 'decimal:2',
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

    /**
     * Submit detailed grade components
     */
    public function submitDetailedGrades(array $grades, string $comments = null): bool
    {
        $gradeData = [
            'presentation_grade' => $grades['presentation'] ?? null,
            'content_grade' => $grades['content'] ?? null,
            'methodology_grade' => $grades['methodology'] ?? null,
            'innovation_grade' => $grades['innovation'] ?? null,
            'questions_grade' => $grades['questions'] ?? null,
            'comments' => $comments,
        ];

        // Calculate overall grade from components
        $gradeData['individual_grade'] = $this->calculateWeightedGrade($grades);

        $this->update($gradeData);

        return true;
    }

    /**
     * Calculate weighted grade from components
     */
    public function calculateWeightedGrade(array $grades = null): float
    {
        if ($grades === null) {
            $grades = [
                'presentation' => $this->presentation_grade,
                'content' => $this->content_grade,
                'methodology' => $this->methodology_grade,
                'innovation' => $this->innovation_grade,
                'questions' => $this->questions_grade,
            ];
        }

        $totalWeightedScore = 0;
        $totalWeight = 0;

        $components = [
            'presentation' => $this->presentation_weight,
            'content' => $this->content_weight,
            'methodology' => $this->methodology_weight,
            'innovation' => $this->innovation_weight,
            'questions' => $this->questions_weight,
        ];

        foreach ($components as $component => $weight) {
            if (!empty($grades[$component]) && $weight > 0) {
                $totalWeightedScore += ($grades[$component] * $weight / 100);
                $totalWeight += $weight;
            }
        }

        return $totalWeight > 0 ? round($totalWeightedScore, 2) : 0;
    }

    /**
     * Get grade components as array
     */
    public function getGradeComponents(): array
    {
        return [
            'presentation' => [
                'grade' => $this->presentation_grade,
                'weight' => $this->presentation_weight,
                'weighted_score' => $this->presentation_grade ? round(($this->presentation_grade * $this->presentation_weight / 100), 2) : 0
            ],
            'content' => [
                'grade' => $this->content_grade,
                'weight' => $this->content_weight,
                'weighted_score' => $this->content_grade ? round(($this->content_grade * $this->content_weight / 100), 2) : 0
            ],
            'methodology' => [
                'grade' => $this->methodology_grade,
                'weight' => $this->methodology_weight,
                'weighted_score' => $this->methodology_grade ? round(($this->methodology_grade * $this->methodology_weight / 100), 2) : 0
            ],
            'innovation' => [
                'grade' => $this->innovation_grade,
                'weight' => $this->innovation_weight,
                'weighted_score' => $this->innovation_grade ? round(($this->innovation_grade * $this->innovation_weight / 100), 2) : 0
            ],
            'questions' => [
                'grade' => $this->questions_grade,
                'weight' => $this->questions_weight,
                'weighted_score' => $this->questions_grade ? round(($this->questions_grade * $this->questions_weight / 100), 2) : 0
            ],
        ];
    }

    /**
     * Check if all grade components are set
     */
    public function hasAllGradeComponents(): bool
    {
        return !empty($this->presentation_grade) &&
               !empty($this->content_grade) &&
               !empty($this->methodology_grade) &&
               !empty($this->innovation_grade) &&
               !empty($this->questions_grade);
    }

    /**
     * Alias for individual_grade field for compatibility
     */
    public function getGradeAttribute(): ?float
    {
        return $this->individual_grade;
    }
}
<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class SubmitEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->hasRole('teacher') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'presentation_score' => [
                'required',
                'numeric',
                'min:0',
                'max:20'
            ],
            'technical_score' => [
                'required',
                'numeric',
                'min:0',
                'max:20'
            ],
            'report_score' => [
                'required',
                'numeric',
                'min:0',
                'max:20'
            ],
            'questions_score' => [
                'required',
                'numeric',
                'min:0',
                'max:20'
            ],
            'overall_comments' => [
                'nullable',
                'string',
                'max:2000'
            ],
            'strengths' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'weaknesses' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'recommendations' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'criteria_scores' => [
                'nullable',
                'array'
            ],
            'criteria_scores.*' => [
                'numeric',
                'min:0',
                'max:5'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'presentation_score.required' => 'Presentation score is required.',
            'presentation_score.min' => 'Presentation score must be at least 0.',
            'presentation_score.max' => 'Presentation score cannot exceed 20.',
            'technical_score.required' => 'Technical score is required.',
            'technical_score.min' => 'Technical score must be at least 0.',
            'technical_score.max' => 'Technical score cannot exceed 20.',
            'report_score.required' => 'Report score is required.',
            'report_score.min' => 'Report score must be at least 0.',
            'report_score.max' => 'Report score cannot exceed 20.',
            'questions_score.required' => 'Questions handling score is required.',
            'questions_score.min' => 'Questions handling score must be at least 0.',
            'questions_score.max' => 'Questions handling score cannot exceed 20.',
            'overall_comments.max' => 'Overall comments cannot exceed 2000 characters.',
            'strengths.max' => 'Strengths section cannot exceed 1000 characters.',
            'weaknesses.max' => 'Weaknesses section cannot exceed 1000 characters.',
            'recommendations.max' => 'Recommendations section cannot exceed 1000 characters.',
            'criteria_scores.*.min' => 'Criteria scores must be at least 0.',
            'criteria_scores.*.max' => 'Criteria scores cannot exceed 5.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $defense = $this->route('defense');

            // Check if defense exists and user is authorized to evaluate
            if ($defense) {
                $user = auth()->user();
                $isJuryMember = $defense->jury_president_id === $user->id ||
                               $defense->jury_examiner_id === $user->id ||
                               $defense->jury_supervisor_id === $user->id;

                if (!$isJuryMember) {
                    $validator->errors()->add('authorization', 'You are not authorized to evaluate this defense.');
                }

                // Check if defense is ready for evaluation
                if ($defense->status !== 'scheduled' && $defense->status !== 'in_progress') {
                    $validator->errors()->add('defense_status', 'This defense is not ready for evaluation.');
                }

                // Check if evaluation window is open (defense date has passed)
                if ($defense->defense_date > now()) {
                    $validator->errors()->add('timing', 'Evaluation can only be submitted after the defense date.');
                }
            }

            // Validate score consistency
            $scores = [
                $this->presentation_score,
                $this->technical_score,
                $this->report_score,
                $this->questions_score
            ];

            $finalScore = array_sum($scores) / 4;

            // Check for significant score variations (all scores should be reasonably consistent)
            $maxScore = max($scores);
            $minScore = min($scores);

            if (($maxScore - $minScore) > 15) {
                $validator->errors()->add('score_consistency', 'Score variations seem too large. Please review your evaluation.');
            }

            // Require comments if any score is below 10
            if ($minScore < 10 && empty($this->overall_comments)) {
                $validator->errors()->add('low_score_comments', 'Comments are required when giving scores below 10.');
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure numeric values are properly formatted
        $this->merge([
            'presentation_score' => (float) $this->presentation_score,
            'technical_score' => (float) $this->technical_score,
            'report_score' => (float) $this->report_score,
            'questions_score' => (float) $this->questions_score,
        ]);
    }
}
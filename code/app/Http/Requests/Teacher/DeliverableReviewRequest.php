<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class DeliverableReviewRequest extends FormRequest
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
            'status' => [
                'required',
                'string',
                'in:approved,rejected,needs_revision'
            ],
            'grade' => [
                'nullable',
                'numeric',
                'min:0',
                'max:20'
            ],
            'feedback' => [
                'required',
                'string',
                'max:2000',
                'min:10'
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
            'improvement_suggestions' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'revision_deadline' => [
                'nullable',
                'date',
                'after:today',
                'required_if:status,needs_revision'
            ],
            'rubric_scores' => [
                'nullable',
                'array'
            ],
            'rubric_scores.*' => [
                'integer',
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
            'status.required' => 'Review status is required.',
            'status.in' => 'Invalid review status. Must be approved, rejected, or needs_revision.',
            'grade.min' => 'Grade must be at least 0.',
            'grade.max' => 'Grade cannot exceed 20.',
            'feedback.required' => 'Feedback is required for all reviews.',
            'feedback.min' => 'Feedback must be at least 10 characters.',
            'feedback.max' => 'Feedback cannot exceed 2000 characters.',
            'strengths.max' => 'Strengths section cannot exceed 1000 characters.',
            'weaknesses.max' => 'Weaknesses section cannot exceed 1000 characters.',
            'improvement_suggestions.max' => 'Improvement suggestions cannot exceed 1000 characters.',
            'revision_deadline.required_if' => 'Revision deadline is required when requesting revisions.',
            'revision_deadline.after' => 'Revision deadline must be in the future.',
            'rubric_scores.*.min' => 'Rubric scores must be at least 0.',
            'rubric_scores.*.max' => 'Rubric scores cannot exceed 5.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $deliverable = $this->route('deliverable');

            // Check if deliverable exists and user is authorized to review
            if ($deliverable) {
                $user = auth()->user();

                // Check if user is the supervisor of this deliverable's project
                if ($deliverable->project->supervisor_id !== $user->id) {
                    $validator->errors()->add('authorization', 'You are not authorized to review this deliverable.');
                }

                // Check if deliverable is in reviewable status
                if ($deliverable->status !== 'submitted') {
                    $validator->errors()->add('deliverable_status', 'This deliverable is not available for review.');
                }
            }

            // Validate status-specific requirements
            if ($this->status === 'approved' && !$this->grade) {
                $validator->errors()->add('grade', 'Grade is required when approving a deliverable.');
            }

            if ($this->status === 'rejected') {
                if (empty($this->weaknesses)) {
                    $validator->errors()->add('weaknesses', 'Weaknesses must be specified when rejecting a deliverable.');
                }
                if ($this->grade && $this->grade > 10) {
                    $validator->errors()->add('grade', 'Grade should be 10 or below when rejecting a deliverable.');
                }
            }

            if ($this->status === 'needs_revision') {
                if (empty($this->improvement_suggestions)) {
                    $validator->errors()->add('improvement_suggestions', 'Improvement suggestions are required when requesting revisions.');
                }
                if (!$this->revision_deadline) {
                    $validator->errors()->add('revision_deadline', 'Revision deadline is required when requesting revisions.');
                }
            }

            // Validate grade consistency with rubric scores
            if ($this->rubric_scores && $this->grade) {
                $calculatedGrade = $this->calculateGradeFromRubric($this->rubric_scores);
                $gradeDifference = abs($this->grade - $calculatedGrade);

                if ($gradeDifference > 2) {
                    $validator->errors()->add('grade_consistency', 'Grade should be consistent with rubric scores.');
                }
            }

            // Validate feedback quality for low grades
            if ($this->grade && $this->grade < 10) {
                if (strlen($this->feedback) < 50) {
                    $validator->errors()->add('feedback', 'Detailed feedback is required for low grades (minimum 50 characters).');
                }
            }
        });
    }

    /**
     * Calculate grade from rubric scores
     */
    private function calculateGradeFromRubric(array $rubricScores): float
    {
        if (empty($rubricScores)) {
            return 0;
        }

        $totalScore = array_sum($rubricScores);
        $maxScore = count($rubricScores) * 5; // Assuming 5-point scale

        return ($totalScore / $maxScore) * 20; // Convert to 20-point scale
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure grade is properly formatted
        if ($this->grade) {
            $this->merge([
                'grade' => (float) $this->grade,
            ]);
        }
    }
}
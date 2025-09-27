<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');
        $user = auth()->user();

        if (!$project || !$user) {
            return false;
        }

        // Supervisor can update their own projects
        if ($user->id === $project->supervisor_id) {
            return true;
        }

        // Admin and department heads can update projects
        return $user->hasRole(['admin_pfe', 'chef_master']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $project = $this->route('project');

        return [
            'status' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['assigned', 'in_progress', 'under_review', 'needs_revision', 'ready_for_defense', 'defended', 'completed'])
            ],
            'expected_end_date' => [
                'sometimes',
                'required',
                'date',
                'after:start_date'
            ],
            'actual_end_date' => [
                'nullable',
                'date',
                'after:start_date'
            ],
            'final_grade' => [
                'nullable',
                'numeric',
                'min:0',
                'max:20'
            ],
            'comments' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'external_supervisor' => [
                'sometimes',
                'nullable',
                'string',
                'max:100'
            ],
            'external_company' => [
                'sometimes',
                'nullable',
                'string',
                'max:100'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Invalid project status.',
            'expected_end_date.after' => 'Expected end date must be after start date.',
            'actual_end_date.after' => 'Actual end date must be after start date.',
            'final_grade.min' => 'Grade must be at least 0.',
            'final_grade.max' => 'Grade cannot exceed 20.',
            'comments.max' => 'Comments cannot exceed 1000 characters.',
            'external_supervisor.max' => 'External supervisor name cannot exceed 100 characters.',
            'external_company.max' => 'External company name cannot exceed 100 characters.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $project = $this->route('project');
            $newStatus = $this->status;

            if ($project && $newStatus) {
                $this->validateStatusTransition($validator, $project->status, $newStatus);
            }

            // Validate grade requirements
            if ($this->has('final_grade') && $this->final_grade !== null) {
                if (!in_array($project->status, ['defended', 'completed'])) {
                    $validator->errors()->add('final_grade', 'Final grade can only be set for defended or completed projects.');
                }
            }

            // Validate actual end date requirements
            if ($this->has('actual_end_date') && $this->actual_end_date !== null) {
                if (!in_array($project->status, ['defended', 'completed'])) {
                    $validator->errors()->add('actual_end_date', 'Actual end date can only be set for defended or completed projects.');
                }
            }
        });
    }

    /**
     * Validate status transitions based on business rules.
     */
    private function validateStatusTransition($validator, $currentStatus, $newStatus): void
    {
        $allowedTransitions = [
            'assigned' => ['in_progress'],
            'in_progress' => ['under_review'],
            'under_review' => ['ready_for_defense', 'needs_revision'],
            'needs_revision' => ['under_review'],
            'ready_for_defense' => ['defended'],
            'defended' => ['completed'],
            'completed' => [] // Final status
        ];

        if (!isset($allowedTransitions[$currentStatus])) {
            $validator->errors()->add('status', 'Invalid current project status.');
            return;
        }

        if (!in_array($newStatus, $allowedTransitions[$currentStatus])) {
            $validator->errors()->add('status', "Cannot transition from '{$currentStatus}' to '{$newStatus}'.");
        }

        // Special validations for specific transitions
        switch ($newStatus) {
            case 'ready_for_defense':
                // Check if project has final deliverables
                $project = $this->route('project');
                if ($project && !$project->deliverables()->where('is_final_report', true)->exists()) {
                    $validator->errors()->add('status', 'Project must have a final report before being ready for defense.');
                }
                break;

            case 'defended':
                // Check if defense is scheduled
                $project = $this->route('project');
                if ($project && !$project->defense) {
                    $validator->errors()->add('status', 'Project must have a scheduled defense before marking as defended.');
                }
                break;

            case 'completed':
                // Ensure final grade is provided
                if (!$this->has('final_grade') || $this->final_grade === null) {
                    $validator->errors()->add('final_grade', 'Final grade is required when completing a project.');
                }
                break;
        }
    }
}
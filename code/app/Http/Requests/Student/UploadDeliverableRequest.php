<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadDeliverableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('student');
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200|min:5',
            'description' => 'required|string|max:2000|min:20',
            'deliverable_type' => [
                'required',
                'string',
                Rule::in([
                    'report', 'presentation', 'code', 'documentation',
                    'prototype', 'analysis', 'design', 'testing',
                    'deployment', 'final_report', 'other'
                ])
            ],
            'file' => [
                'required',
                'file',
                'max:51200', // 50MB
                'mimes:pdf,doc,docx,ppt,pptx,zip,rar,tar,gz,txt,md,html,css,js,py,java,cpp,c,php,sql'
            ],
            'version' => 'required|string|max:20',
            'milestone_id' => 'nullable|exists:project_milestones,id',
            'completion_percentage' => 'required|integer|min:0|max:100',
            'submission_notes' => 'nullable|string|max:1000',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:30',
            'is_final' => 'boolean',
            'team_contribution' => 'nullable|array',
            'team_contribution.*.member_id' => 'exists:team_members,id',
            'team_contribution.*.contribution_percentage' => 'integer|min:0|max:100',
            'team_contribution.*.role' => 'string|max:100',
            'dependencies' => 'nullable|array|max:10',
            'dependencies.*' => 'exists:deliverables,id',
            'expected_feedback_areas' => 'nullable|array|max:10',
            'expected_feedback_areas.*' => 'string|max:100'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Deliverable title is required.',
            'title.min' => 'Deliverable title must be at least 5 characters long.',
            'description.required' => 'Deliverable description is required.',
            'description.min' => 'Description must be at least 20 characters long.',
            'deliverable_type.required' => 'Deliverable type selection is required.',
            'deliverable_type.in' => 'Invalid deliverable type selected.',
            'file.required' => 'File upload is required.',
            'file.max' => 'File size cannot exceed 50MB.',
            'file.mimes' => 'File type not supported. Please upload a valid document, code, or archive file.',
            'version.required' => 'Version number is required.',
            'completion_percentage.required' => 'Completion percentage is required.',
            'completion_percentage.min' => 'Completion percentage cannot be less than 0%.',
            'completion_percentage.max' => 'Completion percentage cannot exceed 100%.',
            'tags.max' => 'You cannot add more than 10 tags.',
            'team_contribution.*.contribution_percentage.min' => 'Contribution percentage cannot be less than 0%.',
            'team_contribution.*.contribution_percentage.max' => 'Contribution percentage cannot exceed 100%.',
            'dependencies.max' => 'You cannot specify more than 10 dependencies.',
            'expected_feedback_areas.max' => 'You cannot specify more than 10 feedback areas.'
        ];
    }

    public function attributes(): array
    {
        return [
            'deliverable_type' => 'deliverable type',
            'completion_percentage' => 'completion percentage',
            'submission_notes' => 'submission notes',
            'tags.*' => 'tag',
            'team_contribution.*.member_id' => 'team member',
            'team_contribution.*.contribution_percentage' => 'contribution percentage',
            'dependencies.*' => 'dependency',
            'expected_feedback_areas.*' => 'feedback area'
        ];
    }

    protected function prepareForValidation(): void
    {
        // Set default values if not provided
        if (!$this->has('is_final')) {
            $this->merge(['is_final' => false]);
        }

        if (!$this->has('completion_percentage')) {
            $this->merge(['completion_percentage' => 100]);
        }

        // Clean and prepare team contribution data
        if ($this->has('team_contribution')) {
            $contributions = collect($this->input('team_contribution'))
                ->filter(function ($contribution) {
                    return isset($contribution['member_id']) && !empty($contribution['member_id']);
                })
                ->values()
                ->toArray();

            $this->merge(['team_contribution' => $contributions]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate team contribution percentages sum to 100% if provided
            if ($this->has('team_contribution') && !empty($this->input('team_contribution'))) {
                $totalContribution = collect($this->input('team_contribution'))
                    ->sum('contribution_percentage');

                if ($totalContribution > 100) {
                    $validator->errors()->add('team_contribution', 'Total team contribution cannot exceed 100%.');
                }
            }
        });
    }
}
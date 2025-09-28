<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class DefensePreparationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('student');
    }

    public function rules(): array
    {
        return [
            'checklist_items' => 'required|array',
            'checklist_items.documentation.*' => 'boolean',
            'checklist_items.presentation.*' => 'boolean',
            'checklist_items.demonstration.*' => 'boolean',
            'checklist_items.qa_preparation.*' => 'boolean',
            'checklist_items.logistics.*' => 'boolean',
            'presentation_notes' => 'nullable|string|max:2000',
            'demo_notes' => 'nullable|string|max:2000',
            'qa_preparation' => 'nullable|string|max:2000',
            'additional_notes' => 'nullable|string|max:1000',
            'estimated_presentation_duration' => 'nullable|integer|min:10|max:30',
            'estimated_demo_duration' => 'nullable|integer|min:5|max:20',
            'technical_requirements' => 'nullable|array|max:15',
            'technical_requirements.*' => 'string|max:100',
            'backup_plans' => 'nullable|array|max:10',
            'backup_plans.*' => 'string|max:200',
            'team_roles' => 'nullable|array',
            'team_roles.*.member_id' => 'exists:team_members,id',
            'team_roles.*.role' => 'required_with:team_roles.*.member_id|string|max:100',
            'team_roles.*.responsibilities' => 'nullable|string|max:500',
            'potential_questions' => 'nullable|array|max:20',
            'potential_questions.*' => 'string|max:300',
            'confidence_level' => 'nullable|integer|min:1|max:5',
            'preparation_hours' => 'nullable|integer|min:0|max:200'
        ];
    }

    public function messages(): array
    {
        return [
            'checklist_items.required' => 'Defense checklist is required.',
            'presentation_notes.max' => 'Presentation notes cannot exceed 2000 characters.',
            'demo_notes.max' => 'Demo notes cannot exceed 2000 characters.',
            'qa_preparation.max' => 'Q&A preparation notes cannot exceed 2000 characters.',
            'additional_notes.max' => 'Additional notes cannot exceed 1000 characters.',
            'estimated_presentation_duration.min' => 'Presentation duration must be at least 10 minutes.',
            'estimated_presentation_duration.max' => 'Presentation duration cannot exceed 30 minutes.',
            'estimated_demo_duration.min' => 'Demo duration must be at least 5 minutes.',
            'estimated_demo_duration.max' => 'Demo duration cannot exceed 20 minutes.',
            'technical_requirements.max' => 'You cannot specify more than 15 technical requirements.',
            'backup_plans.max' => 'You cannot specify more than 10 backup plans.',
            'team_roles.*.role.required_with' => 'Role is required when specifying a team member.',
            'potential_questions.max' => 'You cannot specify more than 20 potential questions.',
            'confidence_level.min' => 'Confidence level must be at least 1.',
            'confidence_level.max' => 'Confidence level cannot exceed 5.',
            'preparation_hours.max' => 'Preparation hours cannot exceed 200.'
        ];
    }

    public function attributes(): array
    {
        return [
            'estimated_presentation_duration' => 'estimated presentation duration',
            'estimated_demo_duration' => 'estimated demo duration',
            'technical_requirements.*' => 'technical requirement',
            'backup_plans.*' => 'backup plan',
            'team_roles.*.role' => 'team member role',
            'potential_questions.*' => 'potential question',
            'confidence_level' => 'confidence level',
            'preparation_hours' => 'preparation hours'
        ];
    }
}
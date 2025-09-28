<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetSubjectPreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('student');
    }

    public function rules(): array
    {
        return [
            'preferences' => 'required|array|min:3|max:10',
            'preferences.*.subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
                Rule::unique('subject_preferences', 'subject_id')->where(function ($query) {
                    return $query->where('team_id', $this->team_id);
                })
            ],
            'preferences.*.priority' => 'required|integer|min:1|max:10',
            'preferences.*.motivation' => 'required|string|min:50|max:1000',
            'preferences.*.technical_alignment' => 'required|integer|min:1|max:5',
            'preferences.*.interest_level' => 'required|integer|min:1|max:5',
            'preferences.*.relevant_experience' => 'nullable|string|max:500',
            'team_id' => 'required|exists:teams,id',
            'submission_notes' => 'nullable|string|max:1000',
            'alternative_subjects' => 'nullable|array|max:5',
            'alternative_subjects.*' => 'exists:subjects,id',
            'external_subject_proposal' => 'nullable|string|max:2000',
            'collaboration_preference' => 'nullable|in:individual,pair,group,flexible',
            'availability_constraints' => 'nullable|string|max:500'
        ];
    }

    public function messages(): array
    {
        return [
            'preferences.required' => 'You must select at least one subject preference.',
            'preferences.min' => 'Please select at least 3 subject preferences.',
            'preferences.max' => 'You cannot select more than 10 subject preferences.',
            'preferences.*.subject_id.required' => 'Subject selection is required for each preference.',
            'preferences.*.subject_id.exists' => 'Selected subject does not exist.',
            'preferences.*.subject_id.unique' => 'You cannot select the same subject multiple times.',
            'preferences.*.priority.required' => 'Priority ranking is required for each preference.',
            'preferences.*.priority.min' => 'Priority must be at least 1.',
            'preferences.*.priority.max' => 'Priority cannot exceed 10.',
            'preferences.*.motivation.required' => 'Motivation explanation is required for each preference.',
            'preferences.*.motivation.min' => 'Motivation must be at least 50 characters long.',
            'preferences.*.technical_alignment.required' => 'Technical alignment rating is required.',
            'preferences.*.interest_level.required' => 'Interest level rating is required.',
            'team_id.required' => 'Team selection is required.',
            'team_id.exists' => 'Selected team does not exist.',
            'alternative_subjects.max' => 'You cannot specify more than 5 alternative subjects.',
            'external_subject_proposal.max' => 'External subject proposal cannot exceed 2000 characters.'
        ];
    }

    public function attributes(): array
    {
        return [
            'preferences.*.subject_id' => 'subject',
            'preferences.*.priority' => 'priority',
            'preferences.*.motivation' => 'motivation',
            'preferences.*.technical_alignment' => 'technical alignment',
            'preferences.*.interest_level' => 'interest level',
            'alternative_subjects.*' => 'alternative subject'
        ];
    }

    protected function prepareForValidation(): void
    {
        // Ensure priorities are unique and properly ranked
        if ($this->has('preferences')) {
            $preferences = $this->input('preferences');
            foreach ($preferences as $index => $preference) {
                if (!isset($preference['priority'])) {
                    $preferences[$index]['priority'] = $index + 1;
                }
            }
            $this->merge(['preferences' => $preferences]);
        }
    }
}
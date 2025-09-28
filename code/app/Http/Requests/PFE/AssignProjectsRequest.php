<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;

class AssignProjectsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->hasRole(['chef_master', 'admin_pfe']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'assignment_method' => 'required|in:automatic,manual,hybrid',
            'start_immediately' => 'boolean',
            'notify_participants' => 'boolean',
            'resolve_conflicts' => 'required|in:merit,registration_order,manual',
            'balance_workload' => 'boolean',
            'external_projects_handling' => 'required|in:auto_assign,manual_review',
            'preferences' => 'array',
            'preferences.respect_team_preferences' => 'boolean',
            'preferences.consider_supervisor_capacity' => 'boolean',
            'preferences.department_restrictions' => 'boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'assignment_method.required' => 'Assignment method is required.',
            'assignment_method.in' => 'Invalid assignment method.',
            'resolve_conflicts.required' => 'Conflict resolution method is required.',
            'resolve_conflicts.in' => 'Invalid conflict resolution method.',
            'external_projects_handling.required' => 'External projects handling method is required.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if there are teams ready for assignment
            $readyTeamsCount = \App\Models\Team::where('status', 'validated')
                ->whereDoesntHave('project')
                ->count();

            if ($readyTeamsCount === 0) {
                $validator->errors()->add('teams', 'No teams are ready for project assignment.');
            }

            // Check if there are available subjects
            $availableSubjectsCount = \App\Models\Subject::where('status', 'published')
                ->whereDoesntHave('projects')
                ->count();

            if ($availableSubjectsCount === 0) {
                $validator->errors()->add('subjects', 'No subjects are available for assignment.');
            }

            // Verify department authorization for chef_master role
            $user = auth()->user();
            if ($user && $user->hasRole('chef_master') && $this->preferences['department_restrictions'] ?? false) {
                // Additional validation for department-specific assignments
            }
        });
    }
}
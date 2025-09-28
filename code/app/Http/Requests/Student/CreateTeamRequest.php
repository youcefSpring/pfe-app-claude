<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('student');
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'min:3',
                Rule::unique('teams', 'name')
            ],
            'description' => 'required|string|max:500|min:10',
            'max_members' => 'required|integer|min:2|max:6',
            'visibility' => 'required|in:public,private,invite_only',
            'skills_required' => 'nullable|array|max:10',
            'skills_required.*' => 'string|max:50',
            'preferred_technologies' => 'nullable|array|max:15',
            'preferred_technologies.*' => 'string|max:50',
            'team_goals' => 'nullable|string|max:1000',
            'collaboration_preferences' => 'nullable|string|max:500',
            'meeting_frequency' => 'nullable|in:daily,weekly,bi_weekly,monthly,as_needed',
            'communication_tools' => 'nullable|array|max:10',
            'communication_tools.*' => 'string|max:30'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Team name is required.',
            'name.unique' => 'This team name is already taken. Please choose another.',
            'name.min' => 'Team name must be at least 3 characters long.',
            'description.required' => 'Team description is required.',
            'description.min' => 'Team description must be at least 10 characters long.',
            'max_members.required' => 'Maximum number of members is required.',
            'max_members.min' => 'Team must allow at least 2 members.',
            'max_members.max' => 'Team cannot have more than 6 members.',
            'skills_required.max' => 'You cannot specify more than 10 required skills.',
            'preferred_technologies.max' => 'You cannot specify more than 15 preferred technologies.'
        ];
    }

    public function attributes(): array
    {
        return [
            'max_members' => 'maximum members',
            'skills_required.*' => 'required skill',
            'preferred_technologies.*' => 'preferred technology',
            'communication_tools.*' => 'communication tool'
        ];
    }
}
<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TeamMember;
use App\Models\Team;
use App\Models\User;

class CreateTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->hasRole('student') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:teams,name',
                'regex:/^[a-zA-Z0-9\s\-_]+$/' // Alphanumeric, spaces, hyphens, underscores only
            ],
            'members' => [
                'required',
                'array',
                'min:1',
                'max:3' // Maximum 3 additional members (4 total with leader)
            ],
            'members.*' => [
                'required',
                'integer',
                'exists:users,id',
                'different:' . auth()->id() // Cannot add yourself as member
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Team name is required.',
            'name.unique' => 'This team name is already taken.',
            'name.max' => 'Team name cannot exceed 100 characters.',
            'name.regex' => 'Team name can only contain letters, numbers, spaces, hyphens, and underscores.',
            'members.required' => 'At least one team member is required.',
            'members.min' => 'Teams must have at least 2 members (including leader).',
            'members.max' => 'Teams cannot have more than 4 members total.',
            'members.*.exists' => 'One or more selected users do not exist.',
            'members.*.different' => 'You cannot add yourself as a team member.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Remove duplicates from members array
        if ($this->has('members')) {
            $members = array_unique($this->members);
            $this->merge([
                'members' => array_values($members)
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = auth()->user();

            // Check if current user is already in a team
            if (TeamMember::where('user_id', $user->id)->exists()) {
                $validator->errors()->add('leader', 'You are already a member of another team.');
            }

            // Check if any selected members are already in teams
            $members = $this->members ?? [];
            $existingMembers = TeamMember::whereIn('user_id', $members)->pluck('user_id')->toArray();

            if (!empty($existingMembers)) {
                $userNames = User::whereIn('id', $existingMembers)
                    ->pluck('first_name', 'id')
                    ->values()
                    ->implode(', ');
                $validator->errors()->add('members', "The following users are already in teams: {$userNames}");
            }

            // Check if all members are students
            if (!empty($members)) {
                $nonStudents = User::whereIn('id', $members)
                    ->whereDoesntHave('roles', function($q) {
                        $q->where('name', 'student');
                    })
                    ->pluck('first_name')
                    ->toArray();

                if (!empty($nonStudents)) {
                    $validator->errors()->add('members', 'All team members must be students: ' . implode(', ', $nonStudents));
                }
            }

            // Check department consistency (optional constraint)
            if (!empty($members)) {
                $userDepartment = $user->department;
                $differentDepartments = User::whereIn('id', $members)
                    ->where('department', '!=', $userDepartment)
                    ->exists();

                if ($differentDepartments) {
                    $validator->errors()->add('members', 'All team members must be from the same department.');
                }
            }

            // Validate team size
            $totalSize = count($members) + 1; // +1 for leader
            if ($totalSize < 2 || $totalSize > 4) {
                $validator->errors()->add('size', 'Team size must be between 2 and 4 members.');
            }
        });
    }
}
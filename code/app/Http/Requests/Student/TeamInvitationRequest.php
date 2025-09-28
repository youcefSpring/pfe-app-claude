<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('student');
    }

    public function rules(): array
    {
        return [
            'invitee_email' => [
                'required',
                'email',
                'exists:users,email',
                Rule::exists('users', 'email')->where(function ($query) {
                    $query->whereHas('roles', function ($roleQuery) {
                        $roleQuery->where('name', 'student');
                    });
                })
            ],
            'message' => 'nullable|string|max:500|min:10',
            'role_description' => 'nullable|string|max:300',
            'expected_contribution' => 'nullable|string|max:500',
            'skills_needed' => 'nullable|array|max:10',
            'skills_needed.*' => 'string|max:50',
            'invitation_type' => 'required|in:direct,open_application',
            'expires_at' => 'nullable|date|after:now|before:' . now()->addDays(30)->toDateString(),
            'priority_level' => 'nullable|in:low,medium,high,urgent',
            'interview_required' => 'boolean',
            'portfolio_required' => 'boolean',
            'additional_requirements' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'invitee_email.required' => 'Invitee email address is required.',
            'invitee_email.email' => 'Please provide a valid email address.',
            'invitee_email.exists' => 'No student found with this email address.',
            'message.min' => 'Invitation message must be at least 10 characters long.',
            'message.max' => 'Invitation message cannot exceed 500 characters.',
            'role_description.max' => 'Role description cannot exceed 300 characters.',
            'expected_contribution.max' => 'Expected contribution cannot exceed 500 characters.',
            'skills_needed.max' => 'You cannot specify more than 10 required skills.',
            'invitation_type.required' => 'Invitation type is required.',
            'invitation_type.in' => 'Invalid invitation type selected.',
            'expires_at.after' => 'Invitation expiry date must be in the future.',
            'expires_at.before' => 'Invitation cannot expire more than 30 days from now.',
            'priority_level.in' => 'Invalid priority level selected.',
            'additional_requirements.max' => 'Additional requirements cannot exceed 1000 characters.'
        ];
    }

    public function attributes(): array
    {
        return [
            'invitee_email' => 'invitee email',
            'invitation_type' => 'invitation type',
            'expires_at' => 'expiry date',
            'priority_level' => 'priority level',
            'skills_needed.*' => 'required skill',
            'interview_required' => 'interview requirement',
            'portfolio_required' => 'portfolio requirement'
        ];
    }

    protected function prepareForValidation(): void
    {
        // Set default expiry date if not provided (7 days from now)
        if (!$this->has('expires_at') || empty($this->input('expires_at'))) {
            $this->merge(['expires_at' => now()->addDays(7)->toDateString()]);
        }

        // Set default values for boolean fields
        if (!$this->has('interview_required')) {
            $this->merge(['interview_required' => false]);
        }

        if (!$this->has('portfolio_required')) {
            $this->merge(['portfolio_required' => false]);
        }

        // Set default priority level
        if (!$this->has('priority_level')) {
            $this->merge(['priority_level' => 'medium']);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if user is already in a team (if business logic requires this)
            $user = \App\Models\User::where('email', $this->input('invitee_email'))->first();

            if ($user) {
                $existingTeamMember = \App\Models\TeamMember::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->exists();

                if ($existingTeamMember) {
                    $validator->errors()->add('invitee_email', 'This student is already a member of another team.');
                }

                // Check if user already has a pending invitation to this team
                $team = $this->route('team');
                if ($team) {
                    $existingInvitation = \App\Models\TeamInvitation::where('team_id', $team->id)
                        ->where('invitee_email', $this->input('invitee_email'))
                        ->where('status', 'pending')
                        ->exists();

                    if ($existingInvitation) {
                        $validator->errors()->add('invitee_email', 'This student already has a pending invitation to your team.');
                    }
                }
            }
        });
    }
}
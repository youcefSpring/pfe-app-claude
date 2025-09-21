<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\TeamMember;
use App\Models\User;

class UpdateTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $team = $this->route('team');
        $user = auth()->user();

        // Only team leader or admin can update team
        return $user && $team && (
            $user->id === $team->leader_id ||
            $user->hasRole(['admin_pfe', 'chef_master'])
        );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $team = $this->route('team');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('teams')->ignore($team->id),
                'regex:/^[a-zA-Z0-9\s\-_]+$/'
            ],
            'action' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['add_member', 'remove_member', 'update_info'])
            ],
            'user_id' => [
                'required_if:action,add_member,remove_member',
                'integer',
                'exists:users,id'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'This team name is already taken by another team.',
            'name.max' => 'Team name cannot exceed 100 characters.',
            'name.regex' => 'Team name can only contain letters, numbers, spaces, hyphens, and underscores.',
            'action.in' => 'Invalid action. Must be add_member, remove_member, or update_info.',
            'user_id.required_if' => 'User ID is required for member operations.',
            'user_id.exists' => 'Selected user does not exist.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $team = $this->route('team');
            $action = $this->action;
            $userId = $this->user_id;

            // Check if team can be modified
            if ($team && $team->status === 'assigned') {
                $validator->errors()->add('team', 'Cannot modify team that is already assigned to a project.');
            }

            if ($action === 'add_member') {
                $this->validateAddMember($validator, $team, $userId);
            } elseif ($action === 'remove_member') {
                $this->validateRemoveMember($validator, $team, $userId);
            }
        });
    }

    /**
     * Validate add member action.
     */
    private function validateAddMember($validator, $team, $userId): void
    {
        // Check team size limit
        if ($team->size >= 4) {
            $validator->errors()->add('size', 'Team cannot have more than 4 members.');
            return;
        }

        // Check if user is already in a team
        if (TeamMember::where('user_id', $userId)->exists()) {
            $validator->errors()->add('user_id', 'User is already a member of another team.');
            return;
        }

        // Check if user is a student
        $user = User::find($userId);
        if ($user && !$user->hasRole('student')) {
            $validator->errors()->add('user_id', 'Only students can be team members.');
            return;
        }

        // Check department consistency
        if ($user && $user->department !== $team->leader->department) {
            $validator->errors()->add('user_id', 'Team member must be from the same department as the leader.');
        }
    }

    /**
     * Validate remove member action.
     */
    private function validateRemoveMember($validator, $team, $userId): void
    {
        // Check minimum team size
        if ($team->size <= 2) {
            $validator->errors()->add('size', 'Team must have at least 2 members.');
            return;
        }

        // Check if user is team leader
        if ($userId === $team->leader_id) {
            $validator->errors()->add('user_id', 'Cannot remove team leader.');
            return;
        }

        // Check if user is actually a member
        if (!TeamMember::where('team_id', $team->id)->where('user_id', $userId)->exists()) {
            $validator->errors()->add('user_id', 'User is not a member of this team.');
        }
    }
}